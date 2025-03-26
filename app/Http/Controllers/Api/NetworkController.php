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
use \RouterOS\Client;
use \RouterOS\Query;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RouterOS\Config;

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
     * Get user by email
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
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json($user->makeHidden(['password']));
        } catch (Exception $e) {
            Log::error("Failed to fetch user by email: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch user'], 500);
        }
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
                'password' => Hash::make($request->password),
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
                $request->merge(['password' => Hash::make($request->password)]);
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

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $plan = Plan::findOrFail($request->plan_id);

            DB::beginTransaction();

            // Create user plan subscription record
            $userPlan = UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => $request->start_date,
                'end_date' => date('Y-m-d', strtotime($request->start_date . " +{$plan->time_limit} days")),
                'status' => 'active',
                'payment_status' => $request->payment_status,
            ]);

            // Call Giffitech API to deduct points
            $giffitResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->giffit_api_user_key
            ])->post('https://api.giffitech.com.ng/points/v2/utility/pay-internet.php', [
                'email' => $user->email,
                'amount' => $plan->price,
                'description' => "Paid {$plan->price} for {$plan->name} wifi plan"
            ]);

            if (!$giffitResponse->successful()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Payment failed on Giffitech API',
                    'details' => $giffitResponse->json()
                ], 500);
            }

            // Configure user on MikroTik routers (Existing logic)
            $failedRouters = [];
            $routers = Router::all();
            $currrent_user = null;

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

                    // Configure user on MikroTik
                    $checkQuery = (new Query('/ip/hotspot/user/print'))->where('name', $user->username);
                    $exists = $client->query($checkQuery)->read();

                    if (!empty($exists)) {
                        $updateQuery = (new Query('/ip/hotspot/user/set'))
                            ->equal('.id', $exists[0]['.id'])
                            ->equal('profile', $plan->name)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($updateQuery);
                    } else {
                        $addQuery = (new Query('/ip/hotspot/user/add'))
                            ->equal('name', $user->username)
                            ->equal('password', $request->password)
                            ->equal('profile', $plan->name)
                            ->equal('limit-uptime', "{$plan->time_limit}d");
                        $client->query($addQuery)->read();
                    }

                    $currrent_user = $client->query((new Query('/ip/hotspot/user/print'))->where('name', $user->username))->read();
                    Log::info("User {$user->username} configured on router {$router->name}");
                } catch (Exception $e) {
                    Log::error("Failed to configure user on router {$router->name}: " . $e->getMessage());
                    $failedRouters[] = $router->name;
                }
            }

            $user->plan_id = $plan->id;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'User subscription processed',
                'user_plan' => $userPlan,
                'router_user' => $currrent_user,
                'warnings' => !empty($failedRouters) ? "Failed to configure on routers: " . implode(', ', $failedRouters) : null
            ]);
        } catch (Exception $e) {
            Log::error("Failed to subscribe user to plan: " . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to subscribe user to plan',
                'details' => $e->getMessage()
            ], 500);
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
