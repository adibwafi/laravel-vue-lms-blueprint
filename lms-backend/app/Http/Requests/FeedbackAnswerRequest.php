<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class FeedbackAnswerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'redeem_code' => 'required',
            'scope' => 'required|in:tpm,uk',
            'sequence' => 'required',
            'notes' => 'required',
            'url_file' => '',
        ];
    }
}
