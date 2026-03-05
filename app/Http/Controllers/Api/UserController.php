<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // ✅ GET ALL USERS
    public function index()
    {
        $users = User::with('location')
                     ->orderBy('name')
                     ->get()
                     ->map(function($user) {
                         return [
                             'id'       => $user->id,
                             'name'     => $user->name,
                             'email'    => $user->email,
                             'role'     => $user->role,
                             'location' => $user->location->name ?? 'N/A',
                         ];
                     });

        return response()->json([
            'success' => true,
            'total'   => $users->count(),
            'users'   => $users
        ]);
    }

    // ✅ GET SINGLE USER
    public function show($id)
    {
        $user = User::with('location')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'location' => $user->location->name ?? 'N/A',
            ]
        ]);
    }

    // ✅ CREATE USER
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'role'        => 'required|in:admin,manager,cashier',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => $request->password,
            'role'        => $request->role,
            'location_id' => $request->location_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'location' => $user->location_id,
            ]
        ], 201);
    }

    // ✅ UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'email'       => 'sometimes|required|email|unique:users,email,' . $id,
            'role'        => 'sometimes|required|in:admin,manager,cashier',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'email', 'role', 'location_id'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'location' => $user->location_id,
            ]
        ]);
    }

    // ✅ DELETE USER
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    // ✅ RESET PASSWORD
    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user->update(['password' => $request->password]);
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. User will need to login again.'
        ]);
    }
}