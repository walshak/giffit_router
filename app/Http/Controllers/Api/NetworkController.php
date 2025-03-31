<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Router;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Carbon\Carbon;
use \RouterOS\Client;
use \RouterOS\Query;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RouterOS\Config;
use Illuminate\Support\Str;

class NetworkController extends Controller
{
    /**
     * Get paginated list of routers with search
     */
    public function getRouters(Request $request)
    {
        try {
            $query = Router::query();

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('desc', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 15);
            $routers = $query->paginate($perPage);

            return response()->json($routers);
        } catch (Exception $e) {
            Log::error("Failed to fetch routers: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch routers'], 500);
        }
    }

    /**
     * Add a new router
     */
    public function addRouter(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:routers',
                'desc' => 'nullable|string|max:1000',
                'ip_address' => 'required|unique:routers',
                'port' => 'required|integer|between:1,65535',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $router = Router::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'username' => $request->username,
                'password' => encrypt($request->password),
            ]);

            Log::info("Router added successfully: {$router->name}");
            return response()->json([
                'message' => 'Router added successfully',
                'router' => $router->makeHidden(['password'])
            ]);
        } catch (Exception $e) {
            Log::error("Failed to add router: " . $e->getMessage());
            return response()->json(['error' => 'Failed to add router'], 500);
        }
    }

    /**
     * Update a router
     */
    public function updateRouter(Request $request, $id)
    {
        try {
            $router = Router::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255|unique:routers,name,' . $id,
                'desc' => 'nullable|string|max:1000',
                'ip_address' => 'string|unique:routers,ip_address,' . $id,
                'port' => 'integer|between:1,65535',
                'username' => 'string|max:255',
                'password' => 'string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->filled('password')) {
                $request->merge(['password' => encrypt($request->password)]);
            }

            $router->update($request->all());

            return response()->json([
                'message' => 'Router updated successfully',
                'router' => $router->makeHidden(['password'])
            ]);
        } catch (Exception $e) {
            Log::error("Failed to update router: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update router'], 500);
        }
    }

    /**
     * Delete a router
     */
    public function deleteRouter($id)
    {
        try {
            $router = Router::findOrFail($id);
            $router->delete();

            return response()->json(['message' => 'Router deleted successfully']);
        } catch (Exception $e) {
            Log::error("Failed to delete router: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete router'], 500);
        }
    }

    /**
     * Get paginated list of plans with search
     */
    public function getPlans(Request $request)
    {
        try {
            $query = Plan::query();

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('desc', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 15);
            $plans = $query->paginate($perPage);

            return response()->json($plans);
        } catch (Exception $e) {
            Log::error("Failed to fetch plans: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch plans'], 500);
        }
    }

    /**
     * Add a new plan
     */
    public function addPlan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:plans',
                'desc' => 'nullable|string|max:1000',
                'upload_speed' => 'required|integer|min:1',
                'download_speed' => 'required|integer|min:1',
                'time_limit' => 'required|integer|min:1',
                'price' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $plan = Plan::create($request->all());

            return response()->json([
                'message' => 'Plan added successfully',
                'plan' => $plan
            ]);
        } catch (Exception $e) {
            Log::error("Failed to add plan: " . $e->getMessage());
            return response()->json(['error' => 'Failed to add plan'], 500);
        }
    }

    /**
     * Get paginated list of users with search
     */
    public function showPlan(Request $request, $plan_id)
    {
        try {
            $plan = Plan::where('id', $plan_id)->first();
            return response()->json($plan);
        } catch (Exception $e) {
            Log::error("Failed to fetch plan: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch plan'], 500);
        }
    }

    /**
     * Update a plan
     */
    public function updatePlan(Request $request, $id)
    {
        try {
            $plan = Plan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255|unique:plans,name,' . $id,
                'desc' => 'nullable|string|max:1000',
                'upload_speed' => 'integer|min:1',
                'download_speed' => 'integer|min:1',
                'time_limit' => 'integer|min:1',
                'price' => 'numeric'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $plan->update($request->all());

            return response()->json([
                'message' => 'Plan updated successfully',
                'plan' => $plan
            ]);
        } catch (Exception $e) {
            Log::error("Failed to update plan: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update plan'], 500);
        }
    }

    /**
     * Delete a plan
     */
    public function deletePlan($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->delete();

            return response()->json(['message' => 'Plan deleted successfully']);
        } catch (Exception $e) {
            Log::error("Failed to delete plan: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete plan'], 500);
        }
    }

    /**
     * Get paginated list of users with search
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::query();

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 15);
            $users = $query->paginate($perPage);

            return response()->json($users->makeHidden(['password']));
        } catch (Exception $e) {
            Log::error("Failed to fetch users: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    /**
     * Get paginated list of users with search
     */
    public function showUser(Request $request, $user_id)
    {
        try {
            $user = User::where('id', $user_id)->first();
            return response()->json($user->makeHidden(['password']));
        } catch (Exception $e) {
            Log::error("Failed to fetch users: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    /**
     * Retrieve or generate a plain text password for a user
     *
     * @param User $user
     * @return string
     */
    private function getPlainTextPassword($user)
    {
        // Encrypt and store the password

        $plainPassword = Str::random(6);
        $user->password = Crypt::encryptString($plainPassword);
        $user->save();

        return $plainPassword; // Return the plain password
    }

    /**
     * Add a new user
     */
    public function addUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_-]+$/',
                'password' => 'required|string|min:8',
                'email' => 'required|email|unique:users',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'username' => $request->username,
                'password' => Crypt::encryptString($request->password),
                'email' => $request->email,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user->makeHidden(['password'])
            ]);
        } catch (Exception $e) {
            Log::error("Failed to register user: " . $e->getMessage());
            return response()->json(['error' => 'Failed to register user'], 500);
        }
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'password' => 'nullable|string|min:8',
                'email' => 'email|unique:users,email,' . $id,
                'name' => 'string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->filled('password')) {
                $request->merge(['password' => Crypt::encryptString($request->password)]);
            }

            $user->update($request->all());

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->makeHidden(['password'])
            ]);
        } catch (Exception $e) {
            Log::error("Failed to update user: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            Log::error("Failed to delete user: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }

    /**
     * Subscribe user to a plan and configure on all routers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribeUserToPlan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_identifier' => 'required',
                'plan_id' => 'required|exists:plans,id',
                'start_date' => 'required|date|after_or_equal:today',
                'payment_status' => 'required|in:pending,completed',
                'giffit_api_user_key' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Find user by ID or email
            $user = is_numeric($request->user_identifier)
                ? User::find($request->user_identifier)
                : User::where('email', $request->user_identifier)->first();

            // Create user if they don't exist
            if (!$user) {
                // Generate a unique username for the account (not the plan)
                $baseUsername = strtolower(explode('@', $request->user_identifier)[0]);
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make(Str::random(6)), // Main account password
                    'email' => $request->user_identifier,
                    'name' => $request->user_identifier, // Use email as default name
                    'status' => 'active'
                ]);
            }

            $plan = Plan::findOrFail($request->plan_id);

            // Generate unique hotspot credentials for this subscription
            $hotspotUsername = $this->generateUniqueHotspotUsername($user);
            $hotspotPassword = Str::random(6);

            DB::beginTransaction();

            // Create user plan subscription record with unique credentials
            $userPlan = UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => $request->start_date,
                'end_date' => date('Y-m-d', strtotime($request->start_date . " +{$plan->time_limit} days")),
                'status' => 'active',
                'payment_status' => $request->payment_status,
                'hotspot_username' => $hotspotUsername,
                'hotspot_password' => encrypt($hotspotPassword),
                'notes' => json_encode([
                    'username' => $hotspotUsername,
                    'password' => $hotspotPassword
                ])
            ]);

            // //Call Giffitech API to deduct points
            // $giffitResponse = Http::withHeaders([
            //     'Content-Type' => 'application/json',
            //     'Authorization' => 'Bearer ' . $request->giffit_api_user_key
            // ])->post('https://api.giffitech.com.ng/points/v2/utility/pay-internet.php', [
            //     'email' => $user->email,
            //     'amount' => $plan->price,
            //     'description' => "Paid {$plan->price} for {$plan->name} wifi plan valid for {$plan->time_limit} days with bandwith of {$plan->upload_speed}M/{$plan->download_speed}M"
            // ]);

            // if (!$giffitResponse->successful()) {
            //     DB::rollBack();
            //     return response()->json([
            //         'error' => 'Payment failed on Giffitech API',
            //         'details' => $giffitResponse->json()
            //     ], 500);
            // }

            // Configure subscription on MikroTik routers
            $failedRouters = [];
            $routers = Router::all();
            $currentHotspotUser = null;

            foreach ($routers as $router) {
                try {
                    $config = (new Config())
                        ->set('host', $router->ip_address)
                        ->set('port', $router->port)
                        ->set('user', $router->username)
                        ->set('pass', decrypt($router->password));

                    $client = new Client($config);

                    // Ensure profile exists
                    $profileQuery = (new Query('/ip/hotspot/user/profile/print'))
                        ->where('name', $plan->name);
                    $profileExists = $client->query($profileQuery)->read();

                    if (empty($profileExists)) {
                        $profileCreateQuery = (new Query('/ip/hotspot/user/profile/add'))
                            ->equal('name', $plan->name)
                            ->equal('rate-limit', "{$plan->download_speed}M/{$plan->upload_speed}M");
                        $client->query($profileCreateQuery)->read();
                    }

                    // Configure hotspot user on MikroTik with plan-specific credentials
                    $checkQuery = (new Query('/ip/hotspot/user/print'))->where('name', $hotspotUsername);
                    $exists = $client->query($checkQuery)->read();

                    if (!empty($exists)) {
                        $updateQuery = (new Query('/ip/hotspot/user/set'))
                            ->equal('.id', $exists[0]['.id'])
                            ->equal('profile', $plan->name)
                            ->equal('password', $hotspotPassword)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($updateQuery);
                    } else {
                        $addQuery = (new Query('/ip/hotspot/user/add'))
                            ->equal('name', $hotspotUsername)
                            ->equal('password', $hotspotPassword)
                            ->equal('profile', $plan->name)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($addQuery)->read();
                    }

                    $currentHotspotUser = $client->query((new Query('/ip/hotspot/user/print'))->where('name', $hotspotUsername))->read();
                    Log::info("Hotspot user {$hotspotUsername} configured on router {$router->name}");
                } catch (Exception $e) {
                    Log::error("Failed to configure user on router {$router->name}: " . $e->getMessage());
                    $failedRouters[] = $router->name;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'User subscription processed',
                'user' => $user,
                'subscription' => [
                    'plan' => $plan->name,
                    'hotspot_username' => $hotspotUsername,
                    'hotspot_password' => $hotspotPassword,
                    'start_date' => $userPlan->start_date,
                    'end_date' => $userPlan->end_date
                ],
                'router_user' => $currentHotspotUser ?? null,
                'warnings' => !empty($failedRouters) ? "Failed to configure on routers: " . implode(', ', $failedRouters) : null
            ]);
        } catch (Exception $e) {
            Log::error("Failed to subscribe user to plan: " . $e->getMessage(), [$e]);
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to subscribe user to plan',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique hotspot username based on user account
     *
     * @param User $user
     * @return string
     */
    private function generateUniqueHotspotUsername($user)
    {
        // Base format: first part of email + random string + counter if needed
        $baseName = strtolower(explode('@', $user->email)[0]);
        $random = substr(md5(microtime()), 0, 4); // Short random string
        $username = $baseName . '_' . $random;

        // Check if this username exists in any active UserPlan
        $counter = 1;
        while (UserPlan::where('hotspot_username', $username)
            ->where('status', 'active')
            ->exists()
        ) {
            $username = $baseName . '_' . $random . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Get user by email with plain password and all active subscriptions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserByEmail(Request $request)
    {
        try {
            $email = $request->input('email');

            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                // Generate a unique username
                $baseUsername = strtolower(explode('@', $email)[0]);
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                // Generate a random password
                $plainPassword = Str::random(6);

                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make($plainPassword),
                    'email' => $email,
                    'name' => $baseUsername,
                    'status' => 'active'
                ]);

                $activeSubscriptions = [];
            } else {
                // Get all active subscriptions for this user
                $activeSubscriptions = UserPlan::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->with('plan')
                    ->get()
                    ->map(function ($subscription) {
                        return [
                            'plan_name' => $subscription->plan->name,
                            'hotspot_username' => $subscription->hotspot_username,
                            'hotspot_password' => decrypt($subscription->hotspot_password),
                            'start_date' => $subscription->start_date,
                            'end_date' => $subscription->end_date,
                            'days_remaining' => now()->diffInDays(Carbon::parse($subscription->end_date))
                        ];
                    });
            }

            return response()->json([
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'name' => $user->name,
                'status' => $user->status,
                'subscriptions' => $activeSubscriptions
            ]);
        } catch (Exception $e) {
            Log::error("Failed to fetch user by email: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Clean up expired user plans from all routers
     * This should be run as a scheduled task
     *
     * @return void
     */
    public function cleanupExpiredPlans()
    {
        try {
            Log::info("Starting cleanup of expired plans");

            // Find all expired plans
            $expiredPlans = UserPlan::where('end_date', '<', date('Y-m-d'))
                ->where('status', 'active')
                ->get();

            Log::info("Found {$expiredPlans->count()} expired plans to clean up");

            // For each expired plan, remove from routers and update status
            foreach ($expiredPlans as $plan) {
                // Mark plan as expired in database
                $plan->status = 'expired';
                $plan->save();

                // Remove from all routers
                $routers = Router::all();
                $username = $plan->hotspot_username;

                foreach ($routers as $router) {
                    try {
                        $config = (new Config())
                            ->set('host', $router->ip_address)
                            ->set('port', $router->port)
                            ->set('user', $router->username)
                            ->set('pass', decrypt($router->password));

                        $client = new Client($config);

                        // Find the user on the router
                        $checkQuery = (new Query('/ip/hotspot/user/print'))->where('name', $username);
                        $exists = $client->query($checkQuery)->read();

                        if (!empty($exists)) {
                            // Remove the user
                            $removeQuery = (new Query('/ip/hotspot/user/remove'))
                                ->equal('.id', $exists[0]['.id']);
                            $client->query($removeQuery)->read();
                            Log::info("Removed expired hotspot user {$username} from router {$router->name}");
                        }
                    } catch (Exception $e) {
                        Log::error("Failed to remove expired user {$username} from router {$router->name}: " . $e->getMessage());
                    }
                }
            }

            Log::info("Completed cleanup of expired plans");
            return true;
        } catch (Exception $e) {
            Log::error("Error during cleanup of expired plans: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Helper method to establish router connection
     *
     * @param string $ip
     * @param int $port
     * @param string $username
     * @param string $password
     * @return Client
     * @throws Exception
     */
    private function connectToRouter($ip, $port, $username, $password)
    {
        try {
            $client = new Client([
                'timeout' => 10,
                'host' => $ip,
                'user' => $username,
                'pass' => $password,
                'port' => $port,
            ]);

            // Test connection
            $client->query('/system/identity/print')->read();

            return $client;
        } catch (Exception $e) {
            throw new Exception("Failed to connect to router: {$e->getMessage()}");
        }
    }
}
