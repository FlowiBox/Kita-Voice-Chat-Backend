<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
        $rules = [];
        if ($this->get('type') == 'email_pass') {
            $rules['email'] = ['required','email'];
            $rules['password'] = ['required'];
        }elseif ($this->get ('type') == 'phone_pass'){
            $rules['phone'] = ['required'];
            $rules['password'] = ['required'];
        }elseif ($this->get ('type') == 'google'){
            $rules['google_id'] = ['required'];
        }elseif ($this->get ('type') == 'facebook'){
            $rules['facebook_id'] = ['required'];
        }elseif ($this->get ('type') == 'phone_code'){
            $rules['phone']=['required'];
            $rules['code']=['required'];
        }
        else{
            $rules['phone'] = ['required'];
            $rules['password'] = ['required'];
        }
        return $rules;
    }

    public function failedValidation ( Validator $validator )
    {
        throw new HttpResponseException(response()->json(
            [
                'success'   => false,

                'message'   => __ ('validation errors'),

                'data'      => $validator->errors()
            ]
        ));
    }

    public function messages ()
    {
        return [
            'required'=>__ ('required'),
            'unique'=>__ ('exists')
        ];
    }
}
