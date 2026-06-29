<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class OTPRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'type' => 'required',
        ];
    }
}
