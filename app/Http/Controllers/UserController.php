<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
    
        $user = User::create($validated);
    
        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    }

    public function login(Request $request){
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These credentials do not match our records.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
            'name' => $request->user()->name,
        ], 200);
    }

    public function reservations(Request $request){
        $page = $request->query('page',1);
        
        $reservations = Cache::remember('reservations_for_user_'.$request->user()->id.'_page_'.$page, now()->addMinutes(60), function () use ($request) {
            return $request->user()->reservations()->with("book")->paginate(10);
        });

        return response()->json($reservations);
    }
    public function favorites(Request $request){
        $page = $request->query('page',1);
        
        $favoriteBooks = Cache::remember('favorites_for_user_'.$request->user()->id.'_page_'.$page, now()->addMinutes(60), function () use ($request) {
            return $request->user()->favorites()->paginate(10); 
        });

        return response()->json($favoriteBooks);
    }

    public function update(Request $request,User $user){
        
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => ['nullable','email',Rule::unique('users')->ignore($user)],
            'password' => 'nullable|min:8|confirmed',
        ]);

        
        if (!empty($validatedData['name'])) $user->name = $validatedData['name'];
        if (!empty($validatedData['email'])) $user->email = $validatedData['email'];
        if (!empty($validatedData['password'])) $user->password = bcrypt($validatedData['password']);

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }
}
