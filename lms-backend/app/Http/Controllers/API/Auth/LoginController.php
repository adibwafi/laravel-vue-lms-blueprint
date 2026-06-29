<?php

namespace App\Http\Controllers\API\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController;

class LoginController extends BaseController
{
    public function store(LoginRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->with('roles')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log the failed authentication attempt
            Log::debug('Login failed: credentials mismatch', ['email' => $request->email]);

            return $this->ressError([
                'message' => 'These credentials do not match our records',
                'data' => []
            ]);
        }

        if ($user->is_blocked) {
            // Log the blocked account attempt
            Log::debug('Login failed: account blocked', ['email' => $request->email]);

            return $this->ressError([
                'message' => 'Your account is blocked!, please confirm admin for active back',
                'data' => []
            ]);
        }

        $token = $user->createToken('ApiToken')->plainTextToken;
        $user->getRoleNames();

        // Log the successful login
        Log::debug('Login successful', ['user_id' => $user->id]);

        return $this->ressSuccess([
            'message' => 'Success login',
            'data' => [
                'token' => $token,
                'user' => $user,
            ]
        ]);
    }
}