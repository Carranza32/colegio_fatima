<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StudentRequest;
use App\Models\Course;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StudentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StudentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Student::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/student');
        CRUD::setEntityNameStrings('Estuadiante', 'Estudiantes');

        $this->crud->denyAccess('show');
        $this->crud->enableExportButtons();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'full_name',
            'type' => 'text',
            'label' => 'Nombre completo'
        ]);

        CRUD::addColumn([
            'name' => 'course.name',
            'type' => 'text',
            'label' => 'Curso'
        ]);

        CRUD::addColumn([
            'name' => 'student_card',
            'type' => 'text',
            'label' => 'Carne'
        ]);
        CRUD::addColumn([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Correo'
        ]);
        CRUD::addColumn([
            'name' => 'NIE',
            'type' => 'text',
            'label' => 'nie label'
        ]);

        CRUD::addColumn([
            'name' => 'status_description',
            'label' => __('crud.field.status'),
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == __('crud.status.active')) {
                        return 'badge bg-success';
                    }

                    return 'badge bg-secondary';
                },
            ],
        ]);

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::addFilter([
            'name' => 'course_id',
            'type' => 'select2',
            'label' => 'Curso',
        ], function () {
            return $this->crud->getModel()::with('course')->get()->pluck('course.name', 'course.id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'course_id', $value);
        });

        CRUD::addFilter(
            [
            'name' => 'is_active',
            'type' => 'dropdown',
            'label' => __('crud.field.status'),
        ],
            [
            0 => __('crud.status.inactive'),
            1 => __('crud.status.active'),
        ],
            function ($value) {
                $this->crud->addClause('where', 'is_active', $value);
            }
        );
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(StudentRequest::class);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Nombres'
        ]);

        CRUD::addField([
            'name' => 'last_name',
            'type' => 'text',
            'label' => 'Apellidos'
        ]);

        CRUD::addField([
            'name' => 'student_card',
            'type' => 'text',
            'label' => 'Carne'
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Correo'
        ]);

        CRUD::addField([
            'name' => 'course_id',
            'type' => 'select2',
            'label' => 'Curso',
            'entity' => 'course',
            'attribute' => 'name_letter',
            'model' => \App\Models\Course::class
        ]);

        CRUD::addField([
            'name' => 'is_active',
            'label' => 'Activo',
            'type' => 'switch',
            'default' => 1
        ]);
        // Nuevos campos
    CRUD::addField([
        'name' => 'NIE',
        'type' => 'text',
        'label' => 'NIE'
    ]);

    CRUD::addField([
        'name' => 'grado_a_estudiar',
        'type' => 'enum',
        'label' => 'Grado a Estudiar'
    ]);

    CRUD::addField([
        'name' => 'nombre_segun_partida_de_nacimiento',
        'type' => 'text',
        'label' => 'Nombre según partida de nacimiento'
    ]);

    CRUD::addField([
        'name' => 'edad',
        'type' => 'text',
        'label' => 'Edad'
    ]);

    CRUD::addField([
        'name' => 'lugar_y_fecha_de_nacimiento',
        'type' => 'text',
        'label' => 'Lugar y Fecha de Nacimiento'
    ]);

    CRUD::addField([
        'name' => 'institucion_donde_estudio_anterior',
        'type' => 'text',
        'label' => 'Institución donde Estudió el Año Anterior'
    ]);

    CRUD::addField([
        'name' => 'enfermedad_permanente',
        'type' => 'text',
        'label' => 'Enfermedad Permanente'
    ]);

    CRUD::addField([
        'name' => 'tipo_de_sangre',
        'type' => 'text',
        'label' => 'Tipo de Sangre'
    ]);

    CRUD::addField([
        'name' => 'comentario',
        'type' => 'text',
        'label' => 'Comentario Adicional'
    ]);

    CRUD::addField([
        'name' => 'nombre_del_padre',
        'type' => 'text',
        'label' => 'Nombre del Padre'
    ]);

    CRUD::addField([
        'name' => 'DUI_del_padre',
        'type' => 'text',
        'label' => 'DUI del Padre'
    ]);

    CRUD::addField([
        'name' => 'nombre_de_la_madre',
        'type' => 'text',
        'label' => 'Nombre de la Madre'
    ]);

    CRUD::addField([
        'name' => 'DUI_de_la_madre',
        'type' => 'text',
        'label' => 'DUI de la Madre'
    ]);

    CRUD::addField([
        'name' => 'estado_civil',
        'type' => 'enum',
        'label' => 'Estado Civil',
    ]);

    CRUD::addField([
        'name' => 'fecha_del_matrimonio_civil',
        'type' => 'date',
        'label' => 'Fecha del Matrimonio Civil'
    ]);

    CRUD::addField([
        'name' => 'fecha_del_matrimonio_religioso',
        'type' => 'date',
        'label' => 'Fecha del Matrimonio Religioso'
    ]);

    CRUD::addField([
        'name' => 'religion',
        'type' => 'text',
        'label' => 'Religión'
    ]);

    CRUD::addField([
        'name' => 'estan_juntos',
        'type' => 'enum',
        'label' => 'Están Juntos'
    ]);

    CRUD::addField([
        'name' => 'direccion_particular',
        'type' => 'text',
        'label' => 'Dirección Particular'
    ]);

    CRUD::addField([
        'name' => 'telefono_de_casa',
        'type' => 'text',
        'label' => 'Teléfono de Casa'
    ]);

    CRUD::addField([
        'name' => 'telefono_de_trabajo_del_padre',
        'type' => 'text',
        'label' => 'Teléfono de Trabajo del Padre'
    ]);

    CRUD::addField([
        'name' => 'telefono_celular_del_padre',
        'type' => 'text',
        'label' => 'Teléfono Celular del Padre'
    ]);

    CRUD::addField([
        'name' => 'telefono_de_trabajo_de_la_madre',
        'type' => 'text',
        'label' => 'Teléfono de Trabajo de la Madre'
    ]);

    CRUD::addField([
        'name' => 'telefono_celular_de_la_madre',
        'type' => 'text',
        'label' => 'Teléfono Celular de la Madre'
    ]);

    CRUD::addField([
        'name' => 'casa',
        'type' => 'enum',
        'label' => 'Casa'
    ]);

    CRUD::addField([
        'name' => 'remesas',
        'type' => 'enum',
        'label' => 'Remesas',
    ]);

    CRUD::addField([
        'name' => 'numero_de_personas_en_el_grupo_familiar',
        'type' => 'number',
        'label' => 'Número de Personas en el Grupo Familiar'
    ]);

    CRUD::addField([
        'name' => 'nombre_del_encargado',
        'type' => 'text',
        'label' => 'Nombre del Encargado'
    ]);

    CRUD::addField([
        'name' => 'parentesco_del_encargado',
        'type' => 'text',
        'label' => 'Parentesco del Encargado'
    ]);

    CRUD::addField([
        'name' => 'telefono_del_encargado',
        'type' => 'text',
        'label' => 'Teléfono del Encargado'
    ]);

    CRUD::addField([
        'name' => 'DUI_del_encargado',
        'type' => 'text',
        'label' => 'DUI del Encargado'
    ]);

    CRUD::addField([
        'name' => 'ocupacion_del_padre',
        'type' => 'text',
        'label' => 'Ocupación del Padre'
    ]);

    CRUD::addField([
        'name' => 'lugar_de_trabajo_del_padre',
        'type' => 'text',
        'label' => 'Lugar de Trabajo del Padre'
    ]);

    CRUD::addField([
        'name' => 'cargo_del_padre',
        'type' => 'text',
        'label' => 'Cargo del Padre'
    ]);

    CRUD::addField([
        'name' => 'ocupacion_de_la_madre',
        'type' => 'text',
        'label' => 'Ocupación de la Madre'
    ]);

    CRUD::addField([
        'name' => 'lugar_de_trabajo_de_la_madre',
        'type' => 'text',
        'label' => 'Lugar de Trabajo de la Madre'
    ]);

    CRUD::addField([
        'name' => 'cargo_de_la_madre',
        'type' => 'text',
        'label' => 'Cargo de la Madre'
    ]);

    CRUD::addField([
        'name' => 'documentos',
        'type' => 'select_from_array',
        'label' => 'Documentos',
        'options' => [
            'Partida de nacimiento' => 'Partida de nacimiento',
            'Fotografia' => 'Fotografia',
            'Certificado' => 'Certificado',
            'Diploma' => 'Diploma',
            'Constancia BC' => 'Constancia BC',
            'Vacunas' => 'Vacunas',
            'Copia de DUI de los padres' => 'Copia de DUI de los padres',
            'Tarjeta Calificaciones' => 'Tarjeta Calificaciones',
            'Solvencia de donde estudio' => 'Solvencia de donde estudio'
        ],
        'allows_multiple' => true
    ]);

    CRUD::addField([
        'name' => 'observaciones',
        'type' => 'text',
        'label' => 'Observaciones'
    ]);

    CRUD::addField([
        'name' => 'nacionalidad',
        'type' => 'text',
        'label' => 'Nacionalidad'
    ]);

    CRUD::addField([
        'name' => 'retornado',
        'type' => 'enum',
        'label' => 'Retornado'
    ]);

    CRUD::addField([
        'name' => 'condicion_de_discapacidad',
        'type' => 'select_from_array',
        'label' => 'Condición de Discapacidad',
        'options' => [
            'Ceguera' => 'Ceguera',
            'Sordera' => 'Sordera',
            'Sordo-Ceguera' => 'Sordo-Ceguera',
            'Hipoacusia' => 'Hipoacusia',
            'Síndrome de Down' => 'Síndrome de Down',
            'Ausencia de Miembros' => 'Ausencia de Miembros',
            'Baja Visión' => 'Baja Visión',
            'Multidiscapacidad' => 'Multidiscapacidad',
            'Discapacidad Intelectual' => 'Discapacidad Intelectual',
            'Discapacidad Motora' => 'Discapacidad Motora',
            'Psicosocial' => 'Psicosocial',
            'Trastorno del espectro autista' => 'Trastorno del espectro autista',
            'Posee diagnóstico clínico' => 'Posee diagnóstico clínico',
            'No aplica' => 'No aplica'
        ],
        'allows_multiple' => true
    ]);

    CRUD::addField([
        'name' => 'posee_diagnostico_clinico',
        'type' => 'enum',
        'label' => 'Posee diagnóstico clínico'
    ]);

    CRUD::addField([
        'name' => 'estudiante_referido_a',
        'type' => 'select_from_array',
        'label' => 'Estudiante referido a',
        'options' => [
            'No aplica' => 'No aplica',
            'Docente Apoyo a la inclusión' => 'Docente Apoyo a la inclusión',
            'Centro de Orientación' => 'Centro de Orientación'
        ],
        'allows_multiple' => true
    ]);

    CRUD::addField([
        'name' => 'estudiante_recibe',
        'type' => 'select_from_array',
        'label' => 'Estudiante recibe',
        'options' => ['No aplica' => 'No aplica', 'Terapia de Rehabilitación' => 'Terapia de Rehabilitación', 'Atención Psiquiátrica' => 'Atención Psiquiátrica', 'Terapia de lenguaje' => 'Terapia de lenguaje', 'Fisioterapia' => 'Fisioterapia', 'Atención Neurológica' => 'Atención Neurológica', 'Terapia de Audición y Lenguaje' => 'Terapia de Audición y Lenguaje', 'Atención Psicológica' => 'Atención Psicológica', 'Otros' => 'Otros'],
        'allows_multiple' => true
    ]);

    CRUD::addField([
        'name' => 'correo_electronico_del_estudiante',
        'type' => 'email',
        'label' => 'Correo Electrónico del Estudiante'
    ]);

    CRUD::addField([
        'name' => 'telefono_de_contacto_del_estudiante',
        'type' => 'text',
        'label' => 'Teléfono de Contacto del Estudiante'
    ]);

    CRUD::addField([
        'name' => 'whatsapp',
        'type' => 'enum',
        'label' => 'WhatsApp'
    ]);

    CRUD::addField([
        'name' => 'convivencia_familiar',
        'type' => 'enum',
        'label' => 'Convivencia Familiar'
    ]);

    CRUD::addField([
        'name' => 'cantidad_de_personas_que_viven_con_el_estudiante',
        'type' => 'number',
        'label' => 'Cantidad de Personas que Viven con el Estudiante'
    ]);

    CRUD::addField([
        'name' => 'tiene_acceso_a_internet',
        'type' => 'enum',
        'label' => 'Tiene Acceso a Internet'
    ]);

    CRUD::addField([
        'name' => 'tiene_internet_residencial',
        'type' => 'enum',
        'label' => 'Tiene Internet Residencial'
    ]);

    CRUD::addField([
        'name' => 'compañia_de_internet',
        'type' => 'text',
        'label' => 'Compañía de Internet'
    ]);

    CRUD::addField([
        'name' => 'puede_sintonizar_canal_10',
        'type' => 'enum',
        'label' => 'Puede Sintonizar Canal 10'
    ]);

    CRUD::addField([
        'name' => 'sintoniza_la_franja_educativa',
        'type' => 'enum',
        'label' => 'Sintoniza la Franja Educativa'
    ]);

    CRUD::addField([
        'name' => 'posee_computadora',
        'type' => 'enum',
        'label' => 'Posee Computadora'
    ]);

    CRUD::addField([
        'name' => 'recibira_sus_clases_de_forma',
        'type' => 'enum',
        'label' => 'Recibirá sus Clases de Forma'
    ]);

    CRUD::addField([
        'name' => 'correo_electronico_del_padre_o_madre',
        'type' => 'email',
        'label' => 'Correo Electrónico del Padre o Madre'
    ]);

    CRUD::addField([
        'name' => 'ultimo_grado_de_escolaridad_del_padre_o_madre',
        'type' => 'enum',
        'label' => 'Último Grado de Escolaridad del Padre o Madre'
    ]);
    }

    

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
