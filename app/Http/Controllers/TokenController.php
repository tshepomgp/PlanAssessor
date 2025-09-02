<?php

namespace App\Http\Controllers;

use App\Models\TokenPackage;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
   /* public function __construct()
    {
        $this->middleware('auth');
    } */

    public function index()
    {
        $user = auth()->user();
        $tokenBalance = $user->getTokenBalance();
        $packages = TokenPackage::getActivePackages();
        
        $transactions = $user->tokenTransactions()
            ->with('plan')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_purchased' => $user->tokenTransactions()->where('type', 'purchase')->sum('tokens'),
            'total_used' => $user->tokenTransactions()->where('type', 'usage')->sum(\DB::raw('ABS(tokens)')),
            'total_spent' => $user->tokenTransactions()->where('type', 'purchase')->sum('amount'),
            'avg_per_assessment' => $user->tokenTransactions()
                ->where('type', 'usage')
                ->whereNotNull('plan_id')
                ->avg(\DB::raw('ABS(tokens)')),
        ];

        return view('tokens.index', compact('tokenBalance', 'packages', 'transactions', 'stats'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:token_packages,id'
        ]);

        $package = TokenPackage::findOrFail($request->package_id);
        
        if (!$package->is_active) {
            return back()->with('error', 'This token package is no longer available.');
        }

        $user = auth()->user();
        $tokenBalance = $user->getTokenBalance();

        // In a real application, you would integrate with a payment processor here
        // For now, we'll simulate the purchase (you'll need to add actual payment integration)
        
        try {
            DB::beginTransaction();

            // Add tokens to user's balance
            $tokensToAdd = $package->total_tokens;
            $tokenBalance->addTokens(
                $tokensToAdd,
                "Purchased {$package->name} package",
                $package->price,
                [
                    'package_id' => $package->id,
                    'base_tokens' => $package->tokens,
                    'bonus_tokens' => $tokensToAdd - $package->tokens,
                    'payment_method' => 'credit' // In real app, this would be from payment processor
                ]
            );

            DB::commit();

            return back()->with('success', "Successfully purchased {$tokensToAdd} tokens for R{$package->price}!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Purchase failed: ' . $e->getMessage());
        }
    }

    public function statement(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $transactions = $user->tokenTransactions()
            ->with('plan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'opening_balance' => $user->tokenTransactions()
                ->where('created_at', '<', $startDate)
                ->sum('tokens'),
            'purchased' => $transactions->where('type', 'purchase')->sum('tokens'),
            'used' => $transactions->where('type', 'usage')->sum('tokens'), // Already negative
            'closing_balance' => $user->getTokenBalance()->balance,
            'total_spent' => $transactions->where('type', 'purchase')->sum('amount'),
        ];

        if ($request->has('download')) {
            $pdf = \PDF::loadView('tokens.statement-pdf', compact('user', 'transactions', 'summary', 'startDate', 'endDate'));
            return $pdf->download("token-statement-{$startDate}-to-{$endDate}.pdf");
        }

        return view('tokens.statement', compact('transactions', 'summary', 'startDate', 'endDate'));
    }
}