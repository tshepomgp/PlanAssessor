<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\AssessPlanJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
           // Fetch all plans for the logged-in user
    $userPlans = auth()->user()->plans()->orderBy('updated_at', 'desc')->get();

    // Group by folder name (slug of client name)
    $plans = $userPlans->groupBy(function ($plan) {
        return Str::slug($plan->client_name);
    });

    return view('plans.index', compact('plans'));
        
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plan' => 'required|mimes:pdf',
            'client_name' => 'required|string|max:255',
        ]);
    
        $file = $request->file('plan');
    
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $randomDigits = rand(1000000, 9999999);
        $newFilename = $originalName . '_' . $randomDigits . '.' . $extension;
    
        $clientNameOriginal = $request->input('client_name');
        $clientNameSlug = Str::slug($clientNameOriginal); // ✅ now defined
        
    
        $path = $file->storeAs("plans/{$clientNameSlug}", $newFilename);

        $plan = Plan::create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'client_name' => $request->input('client_name'),
            'client_slug' => $clientNameSlug, 
            'status' => 'processing',
        ]);

        // Placeholder for OCR/GPT job
        // AssessPlanJob::dispatch($plan);
        
       dispatch(new AssessPlanJob($plan));
       
       

        return back()->with('success', 'Plan uploaded! Assessment in progress.');
    }

    public function download(Plan $plan)
{
    $pdf = PDF::loadView('pdf.assessment', ['plan' => $plan]);
    return $pdf->download('assessment_' . $plan->id . '.pdf');
}



public function viewClientFolder($client)
{
    // Show all files in a given client's folder
    $files = Storage::files("plans/{$client}");

    // Get the original plan records (optional, if using DB to track plans)
    $plans = Plan::where('file_path', 'like', "plans/{$client}/%")->get();

    return view('plans.client', compact('client', 'files', 'plans'));
}


public function listClientFolders()
{
    // Get client folders based on plans table
    $folders = Plan::select('client_name', 'client_slug')
    ->where('user_id', auth()->id())
    ->whereNotNull('client_slug')
    ->groupBy('client_name', 'client_slug')
    ->orderBy('client_name')
    ->get()
    ->map(function ($plan) {
        return [
            'slug' => $plan->client_slug,
            'original' => $plan->client_name,
        ];
    })
    ->values();

    return view('plans.index', compact('folders'));
}

}
