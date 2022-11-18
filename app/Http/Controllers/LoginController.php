<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    protected $username;

    public function __construct() {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function findUsername() {
        $login = request()->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username() {
        return $this->username;
    }

    public function authentication(Request $request) {
        $this->validate($request, [
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $request->merge([
            $loginType => $request->input('login')
        ]);

        if (Auth::attempt($request->only($loginType, 'password'))) {
            return redirect()->intended('/');
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'login' => 'Ihre Anmeldedaten stimmen nicht mit unseren Nutzern überein.'
            ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
