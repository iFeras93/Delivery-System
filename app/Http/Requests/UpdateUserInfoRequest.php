<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateUserInfoRequest extends FormRequest
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
    public function rules(Request $request)
    {

        /**
         * Update
         */

        if ($request->has('change_info')) {
            return [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $this->get('id'),
                'type' => 'required',
            ];
        } elseif ($request->has('change_password')) {
            return [
                'current_password' => 'required|min:6',
                'new_password' => 'required|min:6|different:current_password',
                'password_confirmation' => 'required|min:6|same:new_password',
            ];
        }

    }
}
