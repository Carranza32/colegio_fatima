<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodRequest extends FormRequest
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
            'evaluaciones_pruebas_objetivas' => 'required|array',
            'evaluaciones_pruebas_objetivas.*.name' => 'required|string',

            'evaluaciones_actividades' => 'required|array',
            'evaluaciones_actividades.*.name' => 'required|string',
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
            'evaluaciones_pruebas_objetivas' => 'evaluaciones de pruebas objetivas',
            'evaluaciones_pruebas_objetivas.*.name' => 'nombre de la prueba objetiva',

            'evaluaciones_actividades' => 'evaluaciones de actividades',
            'evaluaciones_actividades.*.name' => 'nombre de la actividad',
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
