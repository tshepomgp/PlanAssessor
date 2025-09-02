<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Token;
use App\Models\TokenTransaction;
use App\Models\TokenPackage;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class AdminController extends Controller
{
   /* public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }
*/
public function dashboard()
{
    $stats = [
        'total_users' => \App\Models\User::count(),
        'active_users' => \App\Models\User::where('status', 'active')->count(),
        'pending_users' => \App\Models\User::where('status', 'pending')->count(),
        'total_plans' => \App\Models\Plan::count(),
        'pending_plans' => \App\Models\Plan::where('status', 'processing')->count(),
        'verified_plans' => \App\Models\Plan::where('verification_status', 'verified')->count(),
        'total_tokens_sold' => \App\Models\TokenTransaction::where('type', 'purchase')->sum('tokens'),
        'total_tokens_used' => \App\Models\TokenTransaction::where('type', 'usage')->sum(\DB::raw('ABS(tokens)')),
        'revenue_this_month' => \App\Models\TokenTransaction::where('type', 'purchase')
            ->whereMonth('created_at', now()->month)
            ->sum('amount'),
        'revenue_last_month' => \App\Models\TokenTransaction::where('type', 'purchase')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount'),
    ];

    // Other data for dashboard
    $recent_users = \App\Models\User::latest()->take(5)->get();
    $recent_plans = \App\Models\Plan::with('user')->latest()->take(5)->get();
    $recent_transactions = \App\Models\TokenTransaction::with('user')->latest()->take(10)->get();
    
    $monthly_revenue = [];
    $plan_status_counts = [];

    return view('admin.dashboard', compact(
        'stats', 'recent_users', 'recent_plans', 'recent_transactions',
        'monthly_revenue', 'plan_status_counts'
    ));
}

    // User Management
    public function users(Request $request)
    {
        $query = User::with(['role', 'tokens']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(20);
        $roles = UserRole::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function showUser(User $user)
    {
        $user->load(['role', 'tokens', 'plans', 'tokenTransactions' => function ($q) {
            $q->latest()->limit(20);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,pending',
            'notes' => 'nullable|string|max:500'
        ]);

        $user->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'User status updated successfully.');
    }

    public function adjustUserTokens(Request $request, User $user)
    {
        $request->validate([
            'tokens' => 'required|integer|min:-10000|max:10000',
            'description' => 'required|string|max:255'
        ]);

        $tokenBalance = $user->getTokenBalance();
        
        if ($request->tokens > 0) {
            $tokenBalance->addTokens($request->tokens, $request->description, 0, [
                'admin_adjustment' => true,
                'admin_id' => auth()->id()
            ]);
        } else {
            $tokenBalance->deductTokens(abs($request->tokens), $request->description, null, [
                'admin_adjustment' => true,
                'admin_id' => auth()->id()
            ]);
        }

        return back()->with('success', 'Token balance adjusted successfully.');
    }

    // Plan Management & Verification
    public function plans(Request $request)
    {
        $query = Plan::with(['user', 'verifiedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        $plans = $query->latest()->paginate(20);

        return view('admin.plans.index', compact('plans'));
    }

    public function showPlan(Plan $plan)
    {
        $plan->load(['user', 'verifiedBy', 'tokenTransactions']);
        return view('admin.plans.show', compact('plan'));
    }

    public function verifyPlan(Request $request, Plan $plan)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'notes' => 'required_if:action,reject|nullable|string|max:1000'
        ]);

        if ($request->action === 'verify') {
            $plan->markAsVerified(auth()->id(), $request->notes);
            $message = 'Plan verified successfully.';
        } else {
            $plan->markAsRejected(auth()->id(), $request->notes);
            $message = 'Plan rejected.';
        }

        return back()->with('success', $message);
    }

    // Token Package Management
    public function tokenPackages()
    {
        $packages = TokenPackage::orderBy('sort_order')->orderBy('price')->get();
        return view('admin.token-packages.index', compact('packages'));
    }

    public function storeTokenPackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'tokens' => 'required|integer|min:1|max:100000',
            'price' => 'required|numeric|min:0.01|max:10000',
            'bonus_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        TokenPackage::create($request->all());

        return back()->with('success', 'Token package created successfully.');
    }

    public function updateTokenPackage(Request $request, TokenPackage $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'tokens' => 'required|integer|min:1|max:100000',
            'price' => 'required|numeric|min:0.01|max:10000',
            'bonus_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $package->update($request->all());

        return back()->with('success', 'Token package updated successfully.');
    }

    // Analytics & Reports
    public function analytics()
    {
        $dateRange = request('range', '30'); // days
        $startDate = now()->subDays($dateRange);

        $data = [
            'user_growth' => User::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'revenue_trend' => TokenTransaction::where('type', 'purchase')
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'plan_submissions' => Plan::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'token_usage' => TokenTransaction::where('type', 'usage')
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, SUM(ABS(tokens)) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.analytics', compact('data', 'dateRange'));
    }

    // System Settings
    public function settings()
    {
        return view('admin.settings');
    }


    public function updateUserRole(Request $request, User $user)
{
    $request->validate([
        'role_id' => 'nullable|exists:user_roles,id',
        'role_change_reason' => 'nullable|string|max:500'
    ]);

    $oldRole = $user->role ? $user->role->name : 'none';
    $newRole = $request->role_id ? UserRole::find($request->role_id)->name : 'none';

    $user->update(['role_id' => $request->role_id]);

    // Log the role change
    if ($request->role_change_reason) {
        $user->update([
            'notes' => $user->notes . "\n" . now()->format('Y-m-d H:i') . 
                      " - Role changed from '{$oldRole}' to '{$newRole}' by " . auth()->user()->name . 
                      ": " . $request->role_change_reason
        ]);
    }

    return back()->with('success', "User role updated from '{$oldRole}' to '{$newRole}'.");
}
}