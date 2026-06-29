<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rq = request()->segment(3);
        if ($rq == 'update-password') {
            return [
                'password' => $this->valPassword('confirm'),
            ];
        }
        if (request()->segment(4) == 'is-blocked') {
            return [
                'is_blocked' => 'required|boolean',
            ];
        }
        return [
            'fullname' => 'required',
            'phone' => 'required',
            // 'image' => $this->valImage(request()->segment(3)),
        ];
    }
}
