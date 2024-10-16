<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf|max:2048',
            'report_type' => 'required',
            'content' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File báo cáo không được để trống',
            'file.file' => 'File báo cáo sai định dạng',
            'file.mimes' => 'File báo cáo sai định dạng',
            'file.max' => 'File báo cáo không vượt quá 2MB',
            'report_type.required' => 'Vui lòng chọn loại báo cáo',
            'content.required' => 'Nội dung báo cáo không được để trống',
        ];
    }
}
