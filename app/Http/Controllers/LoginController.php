<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $password = $request->get('password');
        $login = $request->get('login');
        $user = User::where('login', $login)->firstOrFail();
        if (Hash::check($password, $user->password)) {
            $user->revoke(); // TODO revoke tokens
            return $user->createToken("test")->plainTextToken;
        }
        return abort(401, 'Password not correct');
    }
}
