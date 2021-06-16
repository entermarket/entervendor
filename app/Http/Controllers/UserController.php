<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $validated =  $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        return User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'address' => $request->address,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'phoneNumber' => $request->phoneNumber,
            'profileImage' => $request->profileImage,
            'password' => Hash::make($request->password)

        ]);
    }
}
