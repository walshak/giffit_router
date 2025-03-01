<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Router;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Hash;

class WebNetworkController extends Controller
{
    /**
     * Display dashboard
     */
    public function dashboard()
    {
        $routerCount = Router::count();
        $planCount = Plan::count();
        $userCount = User::count();
        $activeSubscriptions = UserPlan::where('status', 'active')->count();

        return view('dashboard', compact('routerCount', 'planCount', 'userCount', 'activeSubscriptions'));
    }

    /**
     * Display routers page
     */
    public function routers()
    {
        $routers = Router::latest()->paginate(10);
        return view('routers', compact('routers'));
    }

    /**
     * Display plans page
     */
    public function plans()
    {
        $plans = Plan::latest()->paginate(10);
        return view('plans', compact('plans'));
    }

    /**
     * Display users page
     */
    public function users()
    {
        $users = User::latest()->paginate(10);
        $plans = Plan::all();
        return view('users', compact('users', 'plans'));
    }

    /**
     * Store router
     */
    public function storeRouter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:routers',
            'desc' => 'nullable|string|max:1000',
            'ip_address' => 'required|unique:routers',
            'port' => 'required|integer|between:1,65535',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $validated['password'] = encrypt($validated['password']);
        Router::create($validated);

        return redirect()->route('routers')->with('success', 'Router added successfully');
    }

    /**
     * Update router
     */
    public function updateRouterWeb(Request $request, $id)
    {
        $router = Router::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255|unique:routers,name,' . $id,
            'desc' => 'nullable|string|max:1000',
            'ip_address' => 'string|unique:routers,ip_address,' . $id,
            'port' => 'integer|between:1,65535',
            'username' => 'string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = encrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $router->update($validated);

        return redirect()->route('routers')->with('success', 'Router updated successfully');
    }

    /**
     * Delete router
     */
    public function destroyRouter($id)
    {
        $router = Router::findOrFail($id);
        $router->delete();

        return redirect()->route('routers')->with('success', 'Router deleted successfully');
    }

    /**
     * Store plan
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans',
            'desc' => 'nullable|string|max:1000',
            'upload_speed' => 'required|integer|min:1',
            'download_speed' => 'required|integer|min:1',
            'time_limit' => 'required|integer|min:1',
        ]);

        Plan::create($validated);

        return redirect()->route('plans')->with('success', 'Plan added successfully');
    }

    /**
     * Update plan
     */
    public function updatePlanWeb(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255|unique:plans,name,' . $id,
            'desc' => 'nullable|string|max:1000',
            'upload_speed' => 'integer|min:1',
            'download_speed' => 'integer|min:1',
            'time_limit' => 'integer|min:1',
        ]);

        $plan->update($validated);

        return redirect()->route('plans')->with('success', 'Plan updated successfully');
    }

    /**
     * Delete plan
     */
    public function destroyPlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->route('plans')->with('success', 'Plan deleted successfully');
    }

    /**
     * Store user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users')->with('success', 'User added successfully');
    }

    /**
     * Update user
     */
    public function updateUserWeb(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => 'email|unique:users,email,' . $id,
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users')->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully');
    }

    /**
     * Subscribe user to plan
     */
    public function subscribeUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date|after_or_equal:today',
            'payment_status' => 'required|in:pending,completed',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $plan = Plan::findOrFail($validated['plan_id']);

        // Create subscription
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => $validated['start_date'],
            'end_date' => date('Y-m-d', strtotime($validated['start_date'] . " +{$plan->time_limit} days")),
            'status' => 'active',
            'payment_status' => $validated['payment_status'],
        ]);

        // Update user plan ID
        $user->plan_id = $plan->id;
        $user->save();

        // For simplicity, we're not handling the router config here as that would be handled by the API
        return redirect()->route('users')->with('success', 'User subscribed to plan successfully');
    }
}
