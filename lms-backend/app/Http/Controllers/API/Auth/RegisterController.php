<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RegisterController extends BaseController
{
    public function store(RegisterRequest $request)
    {
        $request->validated();
        $input = $request->toArray();
        $randOtp = random_int(100000, 999999);

        try {
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $otp = $user->otp()->create([
                'code' => $randOtp,
                'type' => 'verify-email',
                'expired_at' => Carbon::now()->addMinutes(30),
            ]);

            $input['otp'] = $randOtp;
            $input['url'] = $request->url . '/verify-email/' . $randOtp;
            $input['type'] = 'verify-email';
            $this->sendOTP($input);

            return $this->ressSuccess([
                'message' => 'Successfully create user',
                'data' => [
                    'email' => $user->email,
                    'expired_otp' => $otp->expired_at
                ]
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
