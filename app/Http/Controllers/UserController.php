<?php
namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Show all users

  public function index(Request $request)
{
    // Get the role from request, default to 'admin'
    $role = $request->query('role', 'admin');

    // Fetch users with the specified role
    $users = User::where('role', $role)->get();

    // Debugging: Check if users are being retrieved
    // dd($users);

    return view('dashboard.users.index', compact('users', 'role'));
}


    //search email

    // Show single user
    public function show($id)
    {
        $user = User::findOrFail($id); // Ensures 404 if not found
        return view('dashboard.users.show', compact('user'));
    }



    // Show create user form
    public function create(Request $request, $role)
    {
        return view('dashboard.users.create', compact('role'));
    }




   // Store new user - Ensure only admin can create users
   public function store(Request $request)
   {
       $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:users,email',
           'phone_number' => 'required|numeric',
           'role' => 'required|in:admin,vendor,buyer',
           'password' => 'required|min:8|confirmed',
       ]);
   
       User::create([
           'name' => $request->name,
           'email' => $request->email,
           'phone_number' => $request->phone_number,
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
    

    // Ban or unban a user
    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned; // Toggle is_banned column
        $user->save();

        $action = $user->is_banned ? 'banned' : 'unbanned';
        ActivityLogHelper::logActivity($user, $action, "User was {$action}");

        return response()->json(['message' => "User {$action} successfully"]);
    }

    // Search users by name or email
    public function search(Request $request)
    {
        $query = $request->input('query');
        $role = $request->input('role', 'buyer');

        $users = User::where('role', $role)
            ->where(function ($q) use ($query) {
                $q->where('firstname', 'like', "%{$query}%")
                    ->orWhere('lastname', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();
        return view('dashboard.users.index', compact('users', 'role'));
        ;
    }

    // Validate user request
    private function validateUser(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    // Create a user (if needed elsewhere)
    private function createUser(Request $request, $role)
    {
        return User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'role' => $role,
        ]);
    }
}
