<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $request = $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|string|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ]);

        auth()->login($user);
        return redirect()->intended('/');
    }
}
