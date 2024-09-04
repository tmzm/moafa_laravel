<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePrescriptionRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'description' => 'required|min:10',
            'image' => ['required','image','mimes:jpg,jpeg,png,svg'],
        ];
    }


}
