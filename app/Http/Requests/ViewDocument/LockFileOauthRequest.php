<?php

namespace App\Http\Requests\ViewDocument;

use Illuminate\Foundation\Http\FormRequest;

class LockFileOauthRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nuid' => 'required|string|max:255',
            'profile_id' => 'required|integer',
            'unlock_file' => 'required|boolean',
        ];
    }
}
