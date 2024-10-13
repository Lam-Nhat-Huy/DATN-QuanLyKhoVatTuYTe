<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"=> "required|string|max:150",
            "description"=> "required|string",
            "location"=> "required|string|max:150",
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'Không được bỏ trống',
            'name.string' => 'Không đúng định dạng',
            'name.max' => 'Không được vượt quá 150 ký tự',
            
            'description.required' => 'Không được bỏ trống',
            'description.string' => 'Không đúng định dạng',
    
            'location.required' => 'Không được bỏ trống',
            'location.string' => 'Không đúng định dạng',
            'location.max' => 'Không được vượt quá 150 ký tự.',
        ];
    }
}
