<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'buyer');
        $users = User::where('role', $role)->get();
        return view('dashboard.users.index', compact('users', 'role'));
    }
    public function create($role)
    {
        if (!in_array($role, ['admin', 'vendor'])) {
            abort(404);
        }

        return view('dashboard.users.create', compact('role'));
    }
    public function store(Request $request, $role)
    {
        $this->validateUser($request);
        $user = $this->createUser($request, $role);

        ActivityLogHelper::logActivity($user, "create_$role", ucfirst($role) . ' account created');

        return redirect()->route('users.index')->with('success', ucfirst($role) . ' created successfully');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user'));
    }
    public function update(Request $request, $id)
    {
        $user = user::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|numeric',
            'role' => 'required|in:admin,vendor,buyer',
        ]);

        $user->update($request->only('name', 'email', 'phone_number', 'role'));

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
    public function toggleBan($id)
    {
        $user= User::findOrFail($id);
        $action = $user->isBanned() ? 'unban' : 'ban';
        $user->{$action}();

        ActivityLogHelper::logActivity($user, $action, "User was {$action}ned");

        return response()->json(['message' => "User {$action}ned successfully"]);
    }
    private function validateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);
    }
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
