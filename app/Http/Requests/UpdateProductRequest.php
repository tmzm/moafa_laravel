<?php

namespace App\Http\Requests;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
            'scientific_name' => 'min:5|max:50',
            'commercial_name' => 'min:5|max:50',
            'company_name' => 'min:5|max:50',
            'offer' => 'between:1,99',
            'is_offer' => '',
            'is_quantity' => '',
            'quantity' => '',
            'description' => 'min:10',
            'meta_description' => '',
            'meta_subtitle' => '',
            'meta_title' => '',
            'price' => '',
            'expiration' => 'date',
            'image' => ['image','mimes:jpg,jpeg,png,svg,webp'],
            'categories' => 'array'
        ];
    }


}
