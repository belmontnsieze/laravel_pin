<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Mail\WelcomeEmail;

class UserAuthController extends Controller
{
    public function register(Request $request){
        $registerUserData = $request->validate([
            'firstname'=>'required|string',
            'lastname'=>'required|string',
            'phone'=>'required|string',
            'address'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);
        $user = User::create([
            'firstname' => $registerUserData['firstname'],
            'lastname' => $registerUserData['lastname'],
            'phone' => $registerUserData['phone'],
            'address' => $registerUserData['address'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);
        return response()->json([
            'message' => 'User Created ',
        ],201);
    }

    public function login(Request $request){
        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);
        $user = User::where('email',$loginUserData['email'])->first();
        if(!$user || !Hash::check($loginUserData['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        if ($request->user()) {
            return response()->json($request->user(), 200);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    public function logout(){
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'User logged out successfully'], 200);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    public function sendWelcomeEmail(Request $request)
    {
        // Validate the request data
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        // Get the necessary data from the request
        $to = $request->input('to');
        $subject = $request->input('subject');
        $message = $request->input('message');

        // Send the email
        Mail::to($to)->send(new WelcomeEmail($subject, $message));

        return response()->json(['message' => 'Email sent successfully']);
    }
}
