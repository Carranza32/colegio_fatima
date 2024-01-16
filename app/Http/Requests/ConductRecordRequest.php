<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConductRecordRequest extends FormRequest
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
            'name' => 'required',
            'action' => 'required|in:Estudiantes,Docentes', // 'in:Estudiantes,Docentes
            'description' => 'required',
            'student_id' => 'required_if:action,Estudiantes',
            'teacher_id' => 'required_if:action,Docentes',
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
            'student_id' => 'estudiante',
            'name' => 'nombre',
            'description' => 'descripción',
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
