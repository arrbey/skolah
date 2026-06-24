<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('instructor') || $this->user()?->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'min:3', 'max:255'],
            'video_url'       => ['nullable', 'url', 'max:500'],
            'video_duration'  => ['nullable', 'integer', 'min:0', 'max:86400'],
            'content'         => ['nullable', 'string', 'max:100000'],
            'is_free_preview' => ['nullable', 'boolean'],
            'is_published'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Judul lesson wajib diisi.',
            'video_url.url'   => 'URL video harus berupa URL yang valid.',
        ];
    }
}
