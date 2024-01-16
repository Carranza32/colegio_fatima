<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_id' => 'required',
            'name' => 'required',
            'last_name' => 'required',
            'NIE' => 'required',
            'parent_data.*.family_type' => 'required',
            'parent_data.*.names' => 'required',
            'parent_data.*.dui' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'course_id' => 'Grado',
            'name' => 'Nombres',
            'last_name' => 'Apellidos',
            'email' => 'Correo electrónico',
            'NIE' => 'NIE',
            'parent_data' => 'Datos del padre/madre/tutor',
            'parent_data.*.family_type' => 'Parentesco',
            'parent_data.*.names' => 'Nombres',
            'parent_data.*.dui' => 'DUI',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
