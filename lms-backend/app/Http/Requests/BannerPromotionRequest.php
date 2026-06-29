<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class BannerPromotionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => $this->valImage(request()->segment(4)),
            'url'   => 'required|url',
            'name' => 'required',
        ];
    }
}
