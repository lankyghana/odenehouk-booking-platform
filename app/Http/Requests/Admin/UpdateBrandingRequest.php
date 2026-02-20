<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage branding') ?? false;
    }

    public function rules(): array
    {
        return [
            'hero_name' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
            'instagram' => ['nullable', 'url'],
            'tiktok' => ['nullable', 'url'],
            'email' => ['nullable', 'email'],
            'facebook' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
        ];
    }
}
