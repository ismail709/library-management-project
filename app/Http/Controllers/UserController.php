<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    }

    public function login(Request $request){
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These credentials do not match our records.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ], 422); // 422 Unprocessable Entity
        }

        // Generate a token (for API authentication)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request){
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
            'name' => $request->user()->name,
        ], 200);
    }

    public function reservations(Request $request){
        $reservations = $request->user()->reservations()->with("book")->paginate(10); // This retrieves the reservations the user has made
        return response()->json($reservations);
    }
    public function favorites(Request $request){
        $favoriteBooks = $request->user()->favorites()->paginate(10); 
        return response()->json($favoriteBooks);
    }

    public function update(Request $request,User $user){
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => ['nullable','email',Rule::unique('users')->ignore($user)],
            'password' => 'nullable|min:8|confirmed', // Only update if provided
        ]);

        // Update user fields
        if (!empty($validatedData['name'])) $user->name = $validatedData['name'];
        if (!empty($validatedData['email'])) $user->email = $validatedData['email'];
        if (!empty($validatedData['password'])) $user->password = bcrypt($validatedData['password']);

        $user->save(); // Save changes to the database

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }
}
