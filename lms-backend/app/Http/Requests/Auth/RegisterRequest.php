<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class RegisterRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fullname' => 'required|string',
            'phone' => 'required',
            'url' => 'required|url',
            'email' => 'required|unique:users,email',
            'password' => $this->valPassword(),
        ];
    }
}
