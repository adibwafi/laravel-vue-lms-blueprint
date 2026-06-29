<?php

namespace App\Http\Requests;


class VoucherRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rq = request()->segment(4) == 'update' ? '' : 'required';
        return [
            'token' => 'size:10|regex:/[A-Z0-9]{10}/u|' . $rq,
            'course_category_id' => 'uuid|' . $rq
        ];
    }
}
