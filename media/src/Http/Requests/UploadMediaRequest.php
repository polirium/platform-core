<?php

namespace Polirium\Core\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $maxSize = config('media.max_file_size') / 1024; // Convert to KB
        $allowedExtensions = implode(',', config('media.allowed_extensions'));

        return [
            'file' => [
                'required',
                'file',
                'max:' . $maxSize,
            ],
            'collection' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'custom_properties' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded file is invalid.',
            'file.max' => 'The file size must not exceed ' . (config('media.max_file_size') / 1024 / 1024) . 'MB.',
        ];
    }
}
