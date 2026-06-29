<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ForgetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'otp' => 'required|digits:6',
            'email' => 'required|email',
            'password' => $this->valPassword('confirm'),
            'password_confirmation' => $this->valPassword()
        ];
    }
}
