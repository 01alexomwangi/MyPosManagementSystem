<?php

namespace App\Http\Controllers;

use App\Location;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('location')->get();
        $locations = Location::all();

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
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|confirmed|min:6',
            'role'        => 'required|in:cashier,manager,admin',
            'location_id' => 'required|exists:locations,id',
        ]);

        User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => $data['password'], // auto-hashed by mutator
            'role'        => $data['role'],
            'location_id' => $data['location_id'],
        ]);

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing a user
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

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'password'    => 'nullable|confirmed|min:6',
            'role'        => 'required|in:cashier,manager,admin',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user->name        = $data['name'];
        $user->email       = $data['email'];
        $user->role        = $data['role'];
        $user->location_id = $data['location_id'];

        if (!empty($data['password'])) {
            $user->password = $data['password']; // auto-hashed
        }

        $user->save();

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully!');
    }
}
