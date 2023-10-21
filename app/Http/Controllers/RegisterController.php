<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');
        return User::create([
                                'password' => Hash::make($password),
                                'login'    => $login,
                            ]);
    }
}
