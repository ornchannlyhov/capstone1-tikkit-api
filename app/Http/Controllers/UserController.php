<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Show all users
    public function index(Request $request)
    {
        $role = $request->query('role', 'buyer');
        $users = User::where('role', $role)->get();
        return view('dashboard.users.index', compact('users', 'role'));
    }
    public function show($id)
{
    $user = User::find($id);
    dd($user); // This should output user data or NULL if not found.
    return view('dashboard.users.show', compact('user'));
}

  


    // Show create form
    public function create()
    {
        return view('dashboard.users.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'gender' => 'required|in:Male,Female,Other',
            'role' => 'required|in:admin,vendor,user',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|numeric',
            'gender' => 'required|in:Male,Female,Other',
            'role' => 'required|in:admin,vendor,user',
        ]);

        $user->update($request->all());

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    // Delete user
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    // ban or unban a user
    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $action = $user->isBanned() ? 'unban' : 'ban';
        $user->{$action}();

        ActivityLogHelper::logActivity($user, $action, "User was {$action}ned");

        return response()->json(['message' => "User {$action}ned successfully"]);
    }

    // search for a user with name or email
    public function search(Request $request)
    {
        $query = $request->input('query');
        $role = $request->input('role', 'buyer');

        $users = User::where('role', $role)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();

        return response()->json($users);
    }


    // valitdator for user data request 
    private function validateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    // create a user 
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
