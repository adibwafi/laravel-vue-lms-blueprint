<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\OTPRequest;
use App\Http\Requests\Auth\OTPResendRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class OTPController extends BaseController
{
    public function resend(OTPResendRequest $request)
    {
        $request->validated();
        if (isset($request->second)) {
            $exp = Carbon::now()->addSeconds($request->second);
        };
        if (isset($request->minute)) {
            $exp = Carbon::now()->addMinutes($request->minute);
        };
        if (isset($request->hour)) {
            $exp = Carbon::now()->addHours($request->hours);
        };
        if (isset($request->day)) {
            $exp = Carbon::now()->addDays($request->day);
        };

        $randOtp = random_int(100000, 999999);

        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return $this->ressError([
                'message' => 'These email do not match our records',
                'data' => []
            ]);
        }

        $user->otp()->create([
            'code' => $randOtp,
            'type' => $request->type,
            'expired_at' => $exp,
        ]);

        $input['email'] = $request->email;
        $input['otp'] = $randOtp;
        $input['url'] = $request->url . '/' . $request->type . '/' . $randOtp;
        $input['type'] = $request->type;
        $this->sendOTP($input);

        return $this->ressSuccess([
            'message' => 'Successfully resend OTP',
            'data' => []
        ]);
    }

    public function verificationOTP(OTPRequest $request)
    {
        $request->validated();


        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return $this->ressError([
                'message' => 'These email do not match our records',
                'data' => []
            ]);
        }

        $uOtp = $user->otp()->where('type', $request->type)->orderBy('created_at', 'desc')->first();

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

        switch ($request->type) {
            case 'verify-email':
                if ($user->email_verified_at) {
                    return $this->ressSuccess([
                        'message' => 'Email was verified',
                        'data' => []
                    ]);
                }

                $user->email_verified_at = Carbon::now();
                $user->save();
                $token = $user->createToken('ApiToken')->plainTextToken;

                return $this->ressSuccess([
                    'message' => 'Success verified email',
                    'data' => [
                        'token' => $token,
                        'user' => $user,
                    ]
                ]);
                break;

            default:
                return $this->ressSuccess([
                    'message' => 'Success verified OTP',
                    'data' => []
                ]);
                break;
        }
    }
}
