<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "code" => 1,
            "success" => false,
            "message" => "Error validation",
            'data'      => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'password.regex' => "These credentials do not match our records",
        ];
    }

    /**
     * Make validation to global setting
     *
     * @return array
     */

    public function valPassword($data = null)
    {
        $rg = request()->segment(3) == 'login' ? '' : 'regex:"(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"';

        if ($data === 'confirm') {
            return [
                'required',
                $rg,
                'confirmed'
            ];
        }
        return [
            'required',
            $rg,
        ];
    }

    public function valImage($data = null)
    {
        return [
            $data == 'update' ? 'nullable' : 'required',
            'image',
            'max:10240'
        ];
    }

    public function valPdf($data = null)
    {
        return [
            $data == 'update' ? 'nullable' : 'required',
            'max:15000'
        ];
    }

    public function valVideo($data = null)
    {
        return [
            $data == 'update' ? 'nullable' : 'required',
            'mimetypes:video/avi,video/mpeg,video/quicktime',
            'max:102400'
        ];
    }
}
