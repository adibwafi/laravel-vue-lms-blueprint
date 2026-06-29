<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class OTPResendRequest extends BaseRequest
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
            'url' => 'url',
            'second' => 'integer|nullable',
            'minute' => 'integer|nullable',
            'hour' => 'integer|nullable',
            'day' => 'integer|nullable',
            'type' => 'required|string',
        ];
    }
}
