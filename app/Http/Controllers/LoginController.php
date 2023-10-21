<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $password = $request->get('password');
        $login = $request->get('login');
        $user = User::where('login', $login)->firstOrFail();
        if (Hash::check($password, $user->password)) {
            return $user->createToken("test")->plainTextToken;
        }
        return abort(401, 'Password not correct');
    }
}
