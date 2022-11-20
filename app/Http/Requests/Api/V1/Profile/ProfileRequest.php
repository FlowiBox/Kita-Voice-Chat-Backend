<?php

namespace App\Http\Requests\Api\V1\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
            'email'=>[Rule::unique('users')->ignore($this->user()->id),'email'],
            'phone'=>[Rule::unique('users')->ignore($this->user()->id)]
        ];
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
