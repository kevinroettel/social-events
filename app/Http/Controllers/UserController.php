<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    protected $errorCases = [
        'email.email' => 'Die E-Mail-Adresse befindet sich nicht in einem akzeptierten Format.',
        'email.unique' => 'Für diese E-Mail-Adresse existiert bereits ein Account.',
        'username.unique' => 'Dieser Nutzername wird bereits von einem anderen Konto genutzt.'
    ];

    private function rules($password, $isPictureString = false, $id = null) {
        $rules = [
            'username' => 'required|unique:users|string',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed|string|min:6',
            'address' => 'nullable|string',
            'city' => 'nullable|string'
        ];

        if ($password == "null") unset($rules['password']);

        if ($isPictureString) $rules['profile_picture'] = 'nullable|string';
        else $rules['profile_picture'] = 'nullable|image';

        if ($id != null) {
            $rules = array_merge($rules, [
                'email' => [
                    'required', 'email',
                    Rule::unique('users')->ignore($id)
                ],
                'username' => [
                    'required', 'string',
                    Rule::unique('users')->ignore($id)
                ]
            ]);
        }
        
        return $rules;
    }

    public function register(Request $request) {
        $validator = Validator::make(
            $request->all(),
            $this->rules(false),
            $this->errorCases
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $user = new User();
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        auth()->login($user);
        return redirect()->intended('/');
    }

    public function updateProfile($userId, Request $request) {
        $pictureIsString = is_string($request['profile_picture']);
        
        $validator = Validator::make(
            $request->all(),
            $this->rules($request['password'], $pictureIsString, $userId),
            $this->errorCases
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
        $user = Auth::user();
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->address = $validated['address'];
        $user->city = $validated['city'];
        
        if ($pictureIsString) {
            $user->profile_picture = $validated['profile_picture'];
        } else {
            $file = $validated['profile_picture'];
            $name = "/avatars/avatar-" . $user->username . ".". $file->extension();
            $file->storePubliclyAs('public', $name);
            $user->profile_picture = $name;
        }

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return true;
    }

    public function getUsers() {
        $userId = Auth::user()->id;
        $friends = Auth::user()->friends(['id']);

        return User::where('id', '!=', $userId)
            ->whereNotIn('id', $friends)
            ->get()
            ->transform(function ($user, $key) {
                return $user->only(['id', 'username', 'profile_picture']);
            });
    }

    public function getFriendRequests() {
        return Auth::user()->pendingFriendsFrom()->get();
    }

    public function getFriends() {
        return Auth::user()->friends(['id', 'username', 'profile_picture']);
    }
}
