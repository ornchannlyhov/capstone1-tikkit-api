<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Display users based on role (buyer, admin, vendor) or all users
    public function index(Request $request)
    {
        $role = $request->query('role');
        $searchKeyword = $request->query('search');
        $usersQuery = User::query();

        if ($role) {
            $usersQuery->where('role', $role);
        }

        if ($searchKeyword) {
            $usersQuery->where(function ($query) use ($searchKeyword) {
                $query->where('name', 'like', "%$searchKeyword%")
                    ->orWhere('email', 'like', "%$searchKeyword%");
            });
        }
        $users = $usersQuery->paginate(10);

        return view('dashboard.users.index', compact('users', 'role'));
    }

    public function search(Request $request)
    {
        $searchKeyword = $request->query('search');
        $users = User::query()
            ->where('name', 'like', "%$searchKeyword%")
            ->orWhere('email', 'like', "%$searchKeyword%")
            ->get();

        return response()->json(view('users.partials.users_table', compact('users'))->render());
    }

    // Show form to create user (admin, vendor)
    public function create($role)
    {
        $this->validateRole($role);
        return view('dashboard.users.create', compact('role'));
    }

    // Store a new user or vendor
    public function store(Request $request, $role)
    {
        $this->validateRole($role);
        $this->validateUser($request); 

        $user = $this->createUser($request, $role);

        // Log activity
        ActivityLogHelper::logActivity($user, "create_{$role}", ucfirst($role) . ' account created');

        return redirect()->route('users.index')->with('success', ucfirst($role) . ' created successfully');
    }

    // Show user details
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }

    // Edit user details
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user'));
    }

    // Update user information
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validateUpdate($request, $user); 

        // Update the user details, including password if provided.
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'password' => $request->password ? bcrypt($request->password) : $user->password, 
        ]);

        // Log activity
        ActivityLogHelper::logActivity(auth()->user(), "update_user", "Updated user {$user->name} ({$user->id})");

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // Delete a user (soft delete)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        ActivityLogHelper::logActivity(auth()->user(), "delete_user", "Deleted user {$user->name} ({$user->id})");

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    // Toggle ban/unban for a user
    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $action = $user->isBanned() ? 'unban' : 'ban';

        try {
            $user->{$action}(); 
            ActivityLogHelper::logActivity($user, $action, "User was {$action}ned");

            return response()->json([
                'success' => true,
                'message' => "User {$action}ned successfully",
                'is_banned' => $user->isBanned()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ban status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Vendor-specific actions
    public function vendorIndex()
    {
        $users = User::where('role', 'vendor')->get();
        return view('dashboard.users.vendors.index', compact('users'));
    }

    public function vendorCreate()
    {
        return view('dashboard.users.vendors.create');
    }

    public function vendorStore(Request $request)
    {
        $this->validateUser($request);
        $user = $this->createUser($request, 'vendor');

        // Log activity
        ActivityLogHelper::logActivity($user, "create_vendor", 'Vendor account created');

        return redirect()->route('users.vendorIndex')->with('success', 'Vendor created successfully');
    }

    public function vendorEdit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.vendors.edit', compact('user'));
    }

    public function vendorUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validateUpdate($request, $user);

        // Update vendor details
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        // Log activity
        ActivityLogHelper::logActivity(auth()->user(), "update_vendor", "Updated vendor {$user->name} ({$user->id})");

        return redirect()->route('users.vendorIndex')->with('success', 'Vendor updated successfully');
    }

    public function vendorDestroy($id)
    {
        $user = User::findOrFail($id);
        ActivityLogHelper::logActivity(auth()->user(), "delete_vendor", "Deleted vendor {$user->name} ({$user->id})");

        $user->delete();

        return redirect()->route('users.vendorIndex')->with('success', 'Vendor deleted successfully');
    }

    // Helper Functions

    // Validate role for admin or vendor creation
    private function validateRole($role)
    {
        if (!in_array($role, ['admin', 'vendor', 'buyer'])) {
            abort(404, 'Invalid role');
        }
    }

    // Validate user input for creation
    private function validateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    // Validate user input for updates
    private function validateUpdate(Request $request, $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|numeric',
            'role' => 'required|in:admin,vendor,buyer',
        ]);
    }

    // Create a new user with given role
    private function createUser(Request $request, $role)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'role' => $role,
        ]);
    }
}
