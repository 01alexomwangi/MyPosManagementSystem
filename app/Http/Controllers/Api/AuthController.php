<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ✅ REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        $tokenResult = $user->createToken('auth_token');
        $tokenResult->accessToken->expires_at = now()->addHours(24);
        $tokenResult->accessToken->save();
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // ✅ Delete old tokens
        $user->tokens()->delete();

        $tokenResult = $user->createToken('auth_token');
        $tokenResult->accessToken->expires_at = now()->addHours(24);
        $tokenResult->accessToken->save();
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ]
        ]);
    }

    // ✅ LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // ✅ PROFILE
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ]);
    }


      
            // UPDATE PROFILE
            public function updateProfile(Request $request)
            {
                $user = $request->user();

                $validator = Validator::make($request->all(), [
                    'name'  => 'sometimes|required|string|max:255',
                    'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $user->update($request->only(['name', 'email']));

                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'user'    => $user
                ]);
            }

            // CHANGE PASSWORD
            public function changePassword(Request $request)
            {
                $validator = Validator::make($request->all(), [
                    'current_password' => 'required',
                    'new_password'     => 'required|min:6|confirmed',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $user = $request->user();

                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ], 401);
                }

                $user->update(['password' => $request->new_password]);

                return response()->json([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            }
}
