<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Router;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

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


    public function userPlansIndex()
    {
        $userPlans = UserPlan::with(['user', 'plan'])->latest()->paginate(10);
        return view('userplans', compact('userPlans'));
    }

    public function userPlansStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        UserPlan::create($validated);
        return redirect()->route('userplans')->with('success', 'User plan added successfully');
    }

    public function userPlansUpdate(Request $request, $id)
    {
        $userPlan = UserPlan::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'exists:users,id',
            'plan_id' => 'exists:plans,id',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'string',
            'payment_status' => 'string',
        ]);

        $userPlan->update($validated);
        return redirect()->route('userplans')->with('success', 'User plan updated successfully');
    }

    public function userPlansDestroy($id)
    {
        $userPlan = UserPlan::findOrFail($id);
        $userPlan->delete();
        return redirect()->route('userplans')->with('success', 'User plan deleted successfully');
    }

    /**
     * Subscribe user to plan with router configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'plan_id' => 'required|exists:plans,id',
                'start_date' => 'required|date|after_or_equal:today',
                'payment_status' => 'required|in:pending,completed',
            ]);

            $messages = [];
            $user = User::findOrFail($validated['user_id']);
            $plan = Plan::findOrFail($validated['plan_id']);
            $messages[] = "Found user {$user->username} and plan {$plan->name}.";

            // Create subscription
            $userPlan = UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => $validated['start_date'],
                'end_date' => date('Y-m-d', strtotime($validated['start_date'] . " +{$plan->time_limit} days")),
                'status' => 'active',
                'payment_status' => $validated['payment_status'],
            ]);
            $messages[] = "Created subscription record with start date {$validated['start_date']} and end date {$userPlan->end_date}.";

            // Update user plan ID
            $user->plan_id = $plan->id;
            $user->save();
            $messages[] = "Updated user profile with new plan information.";

            // Configure on routers
            $failedRouters = [];
            $successRouters = [];
            $routers = Router::all();
            $messages[] = "Starting configuration on " . count($routers) . " routers.";

            foreach ($routers as $router) {
                try {
                    // Create MikroTik client
                    $config = (new Config())
                        ->set('host', $router->ip_address)
                        ->set('port', $router->port)
                        ->set('user', $router->username)
                        ->set('pass', decrypt($router->password));

                    $client = new Client($config);
                    $messages[] = "Connected to router {$router->name} at {$router->ip_address}.";

                    // Check if profile exists
                    $profileQuery = (new Query('/ip/hotspot/user/profile/print'))
                        ->where('name', $plan->name);
                    $profileExists = $client->query($profileQuery)->read();

                    if (empty($profileExists)) {
                        // Create profile if not exists
                        $profileCreateQuery = (new Query('/ip/hotspot/user/profile/add'))
                            ->equal('name', $plan->name)
                            ->equal('rate-limit', "{$plan->download_speed}M/{$plan->upload_speed}M");
                        $client->query($profileCreateQuery)->read();
                        $messages[] = "Created new profile {$plan->name} with rate limit {$plan->download_speed}M/{$plan->upload_speed}M on router {$router->name}.";
                    } else {
                        $messages[] = "Found existing profile {$plan->name} on router {$router->name}.";
                    }

                    // Check if user exists
                    $checkQuery = (new Query('/ip/hotspot/user/print'))
                        ->where('name', $user->username);
                    $exists = $client->query($checkQuery)->read();

                    if (!empty($exists)) {
                        // Update existing user
                        $updateQuery = (new Query('/ip/hotspot/user/set'))
                            ->equal('.id', $exists[0]['.id'])
                            ->equal('profile', $plan->name)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($updateQuery);
                        $messages[] = "Updated existing user {$user->username} with new profile and time limit {$plan->time_limit} days on router {$router->name}.";
                    } else {
                        // Add new user
                        $addQuery = (new Query('/ip/hotspot/user/add'))
                            ->equal('name', $user->username)
                            ->equal('password', $request->password)
                            ->equal('profile', $plan->name)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($addQuery)->read();
                        $messages[] = "Added new user {$user->username} with time limit {$plan->time_limit} days on router {$router->name}.";
                    }

                    $successRouters[] = $router->name;
                    Log::info("User {$user->username} configured on router {$router->name}");
                } catch (Exception $e) {
                    Log::error("Failed to configure user on router {$router->name}: " . $e->getMessage());
                    $failedRouters[] = $router->name;
                    $messages[] = "Error on router {$router->name}: {$e->getMessage()}.";
                }
            }

            // Prepare summary message
            if (count($successRouters) > 0) {
                $messages[] = "Successfully configured on routers: " . implode(', ', $successRouters) . ".";
            }

            if (count($failedRouters) > 0) {
                $warningMessage = "Failed to configure on routers: " . implode(', ', $failedRouters) . ".";
                $messages[] = $warningMessage;
                // Store warning in session flash
                session()->flash('warning', $warningMessage);
            }

            // Format final message
            $finalMessage = implode(' ', $messages);

            return redirect()->route('users')->with('success', "User subscribed to plan successfully. $finalMessage");
        } catch (Exception $e) {
            Log::error("Failed to subscribe user to plan: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to subscribe user to plan: ' . $e->getMessage()]);
        }
    }
}
