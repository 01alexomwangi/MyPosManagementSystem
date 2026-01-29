<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;
use App\User; // your User model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Show all users (admin only)
     */
    public function index()
    {
        $users = User::with('location')->get(); // for single location
        $locations = Location::all();
        return view('users.index', compact('users','locations'));
    }

  
    public function create()
    {
        return view('users.create');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'location_id' => 'required|exists:locations,id',
            'is_admin' => 'required|integer|in:0,1',
            
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
             'location_id' => $request->location_id,
            'is_admin' => $request->is_admin,
            
        ]);

        // Update password if provided
        if ($request->password) {
            $user->password = $request->password; // password mutator hashes it
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
    public function store(Request $request)
    {

      // dd($request);

    // 1️⃣ Validate the incoming request
    $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6', // expects password_confirmation field
        'location_id' => 'required|exists:locations,id',
     //   'is_admin' => 'required|boolean',
    ]);

    // 2️⃣ Create the user
    $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => Hash::make($data['password']),
        'is_admin' => $request->has('is_admin') ? 1 : 0,
        'location_id' => $request->location_id,
    ]);

    // dd($user);

//    $user->locations()->sync($data['location_id']);  many to many

    // 3️⃣ Redirect back with success message
    return redirect()->route('users.index')
                     ->with('success', 'User created successfully!');
    }

 
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

}
