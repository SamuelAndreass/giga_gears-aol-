<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerRequest extends FormRequest
{
    public function authorize()
    {
        // Bisa diperketat: misal user harus memiliki store dsb.
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
        ];
    }
}
