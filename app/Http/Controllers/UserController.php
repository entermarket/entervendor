<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validated =  $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|unique:users'
        ]);

        return User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'address' => $request->address,
            'dob' => $request->dob,
            'gender' => $request->gender,
        ]);
    }
}
