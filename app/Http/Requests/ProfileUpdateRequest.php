<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],

            // ✅ new academic/profile fields
            'college_name' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'in:'.implode(',', config('academix.departments', []))],
            'registration_no' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'in:'.implode(',', config('academix.semesters', []))],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'sex' => ['nullable', 'in:male,female'],

            // ✅ profile photo
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'remove_profile_photo' => ['nullable', 'boolean'],
        ];
    }
}
