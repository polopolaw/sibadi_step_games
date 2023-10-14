<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $login = $request->get('login');
        if (!$login) {
            return "login is required";
        }
        $password = $request->get('password');
        if (!$password) {
            return "password is required";
        }
        if (strlen($password) < 8) {
            return "password is weak, minimum 8 chars";
        }
        // Юзер уже зареган с таким логином
        if (User::where('login', $login)->exists()) {
            return "login is already exists";
        }
        return User::create([
                                'password' => Hash::make($password),
                                'login'    => $login,
                            ]);
    }
}
