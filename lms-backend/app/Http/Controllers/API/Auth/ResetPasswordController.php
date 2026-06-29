<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\OTPResendPasswordRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Models\User;
use App\Models\UserResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\Auth\OTPRequest;

class ResetPasswordController extends BaseController
{
    public function store(ForgetPasswordRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return $this->ressError([
                'message' => 'These email do not match our records',
                'data' => []
            ]);
        }

        $uOtp = $user->otp()->where('type', 'reset-password')->orderBy('created_at', 'desc')->first();

        if (empty($uOtp)) {
            return $this->ressError([
                'message' => "You haven't requested OTP",
                'data' => []
            ]);
        }

        if ($uOtp->code != $request->otp) {
            return $this->ressError([
                'message' => 'These OTP do not match our records',
                'data' => []
            ]);
        }

        if ($uOtp->expired_at->isPast()) {
            return $this->ressError([
                'message' => 'Your token is expired',
                'data' => []
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        UserResetPassword::create([
            'users_id' => $user->id
        ]);

        return $this->ressSuccess([
            'message' => 'Success change password',
            'data' => []
        ]);
    }
}
