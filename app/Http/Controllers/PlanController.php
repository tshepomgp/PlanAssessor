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
            'plan' => 'required|mimes:pdf|max:10240', // 10MB max
            'client_name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        
        // Check if user can upload plans
       // if (!$user->canUploadPlans()) {
           
      //      return back()->with('error', 'Your account is not approved for plan uploads. Please contact support.');
        //}

        // Check token balance
        $tokenBalance = $user->getTokenBalance();
        $estimatedTokenCost = 75; // Estimated tokens needed for assessment
        
        if (!$tokenBalance->hasEnoughTokens($estimatedTokenCost)) {
            return back()->with('error', "Insufficient tokens. You need at least {$estimatedTokenCost} tokens to process a plan. Please purchase more tokens.");
        }

        $file = $request->file('plan');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $randomDigits = rand(1000000, 9999999);
        $newFilename = $originalName . '_' . $randomDigits . '.' . $extension;
        
        $clientNameOriginal = $request->input('client_name');
        $clientNameSlug = Str::slug($clientNameOriginal);
        
        // Store file in private directory
        //$path = $file->storeAs("plans/{$clientNameSlug}", $newFilename, 'private');
        $path = $file->storeAs("plans/{$clientNameSlug}", $newFilename); // Uses 'local' disk

        // Create plan record
        $plan = Plan::create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'client_name' => $clientNameOriginal,
            'client_slug' => $clientNameSlug, 
            'status' => 'processing',
            'verification_status' => 'pending',
        ]);

        // Dispatch job for assessment
        try {
            AssessPlanJob::dispatch($plan);
            
            return back()->with('success', 
                'Plan uploaded successfully! Assessment in progress. You will be charged approximately ' . 
                $estimatedTokenCost . ' tokens upon completion.');
        } catch (\Exception $e) {
            // If job dispatch fails, clean up
            $plan->delete();
            if (Storage::disk('private')->exists($path)) {
                Storage::disk('private')->delete($path);
            }
            
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function download(Plan $plan)
    {
        // Check if user owns this plan or is admin
        if ($plan->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this plan.');
        }

        if ($plan->status !== 'completed') {
            return back()->with('error', 'Assessment not yet completed.');
        }

        $pdf = PDF::loadView('pdf.assessment', ['plan' => $plan]);
        return $pdf->download('assessment_' . $plan->id . '.pdf');
    }

    public function viewClientFolder($client)
    {
        // Show all files in a given client's folder
        $plans = Plan::where('user_id', auth()->id())
                    ->where('client_slug', $client)
                    ->orderBy('updated_at', 'desc')
                    ->get();

        if ($plans->isEmpty()) {
            return redirect()->route('plans.index')->with('error', 'Client folder not found.');
        }

        // Get the original client name from the first plan
        $clientName = $plans->first()->client_name;
        
        return view('plans.client', compact('client', 'plans'))->with('client', $client);
    }

    public function listClientFolders()
    {
        // Get client folders based on plans table for current user
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

        // Get user token balance for display
        $tokenBalance = auth()->user()->getTokenBalance();

        return view('plans.index', compact('folders', 'tokenBalance'));
    }

    public function show(Plan $plan)
    {
        // Check if user owns this plan or is admin
        if ($plan->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $plan->load(['user', 'verifiedBy', 'tokenTransactions']);
        return view('plans.show', compact('plan'));
    }

    public function retry(Plan $plan)
    {
        // Check if user owns this plan
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this plan.');
        }

        if ($plan->status !== 'failed') {
            return back()->with('error', 'Only failed assessments can be retried.');
        }

        // Check token balance again
        $user = auth()->user();
        $tokenBalance = $user->getTokenBalance();
        $estimatedTokenCost = 75;
        
        if (!$tokenBalance->hasEnoughTokens($estimatedTokenCost)) {
            return back()->with('error', "Insufficient tokens for retry. You need at least {$estimatedTokenCost} tokens.");
        }

        // Reset plan status and retry
        $plan->update([
            'status' => 'processing',
            'assessment' => null,
            'tokens_used' => 0,
            'cost' => 0,
            'ai_responses_metadata' => null,
        ]);

        AssessPlanJob::dispatch($plan);

        return back()->with('success', 'Assessment retry initiated.');
    }
}