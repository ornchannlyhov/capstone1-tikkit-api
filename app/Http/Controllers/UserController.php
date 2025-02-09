<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        return view('dashboard.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:dashboard,vendor,buyer',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|numeric',
            'role' => 'required|in:dashboard,vendor,buyer',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function banOrUnban($id)
    {
        $user = User::findOrFail($id);

        if ($user->isBanned()) {
            $user->unban();
            ActivityLogHelper::logActivity($user, 'unban', 'User was unbanned');
            return response()->json(['message' => 'User unbanned successfully']);
        } else {
            $user->ban();
            ActivityLogHelper::logActivity($user, 'ban', 'User was banned');
            return response()->json(['message' => 'User banned successfully']);
        }
    }
}
