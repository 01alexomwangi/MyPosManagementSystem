<?php

namespace App\Http\Controllers;

use App\Location;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users (admin only)
     */
    public function index()
    {
        $users = User::with('location')->get(); // load location for display
        $locations = Location::all();           // for dropdowns in modals
        return view('users.index', compact('users', 'locations'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $locations = Location::all();
        return view('users.create', compact('locations'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|confirmed|min:6', // password_confirmation field required
            'role'        => 'required|in:cashier,manager,admin',
            'location_id' => 'required|exists:locations,id',
        ]);

        // Create user
        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'],
            'location_id' => $data['location_id'],
            'is_admin'    => $data['role'] === 'admin' ? 1 : 0, // legacy flag
        ]);

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $locations = Location::all();
        return view('users.edit', compact('user', 'locations'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate input
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,'.$user->id,
            'password'    => 'nullable|confirmed|min:6',
            'role'        => 'required|in:cashier,manager,admin',
            'location_id' => 'required|exists:locations,id',
        ]);

        // Update user fields
        $user->name        = $data['name'];
        $user->email       = $data['email'];
        $user->role        = $data['role'];
        $user->location_id = $data['location_id'];
        $user->is_admin    = $data['role'] === 'admin' ? 1 : 0;

        // Update password if provided
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully!');
    }
}
