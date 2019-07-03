<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 * @package App\Http\Requests
 */
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
        return [
            'username' => 'required_without:email|between:3,16|alpha_num|exists:users,username',
            'email'    => 'required_without:username|email|exists:users,email',
            'password' => 'required|between:8,64',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'         => 'The :attribute field is required',
            'required_without' => 'The :attribute field is required if :values is not specified',
            'between'          => 'The :attribute length should be between :min and :max',
            'alpha_num'        => 'The :attribute field is alphabetical and numerical only',
            'exists'           => 'The :attribute is not registered yet',
            'email'            => 'The :attribute should be an email format',
        ];
    }
}
