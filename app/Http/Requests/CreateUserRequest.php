<?php

namespace App\Http\Requests;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateUserRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:50',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|min:5|max:30',
            'role' => 'required',
            // 'image' => ['image','mimes:jpg,jpeg,png,svg'],
            'avatar' => ''
        ];
    }


}
