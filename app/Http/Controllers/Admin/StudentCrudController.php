<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StudentRequest;
use App\Models\Course;
use App\Traits\CheckPermissionsCrud;
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
    use CheckPermissionsCrud;

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

        $this->setupStudents('Estudiante');
        $this->setupParents('Padres');
        // $this->setupOcupation('Ocupación');
        $this->setupDocuments('Información adicional');
        $this->setupSigies('SIGIES');

        CRUD::addField([
            'name' => 'custom_scripts',
            'type' => 'student.custom_scripts',
            'tab' => 'Estudiante',
        ]);
    }

    function setupStudents($tab) {
        CRUD::addField([
            'name' => 'inscription_header',
            'type' => 'view',
            'tab' => $tab,
            'view' => 'partials/inscription_header'
        ]);

        CRUD::addField([
            'name'  => 'html_alumno',
            'type'  => 'custom_html',
            'value' => '<h2>Genarales del alumno<h2/>',
            'tab' => $tab
        ]);

        CRUD::addField([
            'name' => 'NIE',
            'type' => 'text',
            'label' => 'NIE',
            'wrapper' => ['class' => 'col-sm-4 mb-4'],
            'tab' => $tab
        ]);

        CRUD::addField([
            'name' => 'course_id',
            'type' => 'select2',
            'label' => 'Curso',
            'entity' => 'course',
            'attribute' => 'name_letter',
            'model' => \App\Models\Course::class,
            'wrapper' => ['class' => 'col-sm-4 mb-4'],
            'tab' => $tab
        ]);

        CRUD::addField([
            'name' => 'edad',
            'type' => 'text',
            'label' => 'Edad',
            'tab' => $tab,
            'wrapper' => ['class' => 'col-sm-4 mb-4']
        ]);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Nombres',
            'tab' => $tab,
            'wrapper' => ['class' => 'col-sm-6 mb-4']
        ]);

        CRUD::addField([
            'name' => 'last_name',
            'type' => 'text',
            'label' => 'Apellidos',
            'tab' => $tab,
            'wrapper' => ['class' => 'col-sm-6 mb-4']
        ]);
        // Nuevos campos


        CRUD::addField([
            'name' => 'nombre_segun_partida_de_nacimiento',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Nombre según partida de nacimiento'
        ]);


        CRUD::addField([
            'name' => 'lugar_y_fecha_de_nacimiento',
            'type' => 'textarea',
            'tab' => $tab,
            'label' => 'Lugar y Fecha de Nacimiento'
        ]);

        CRUD::addField([
            'name' => 'institucion_donde_estudio_anterior',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Institución donde Estudió el Año Anterior'
        ]);

        CRUD::addField([
            'name' => 'enfermedad_permanente',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Enfermedad Permanente'
        ]);

        CRUD::addField([
            'name' => 'tipo_de_sangre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Tipo de Sangre'
        ]);

        CRUD::addField([
            'name' => 'comentario',
            'type' => 'textarea',
            'tab' => $tab,
            'label' => 'Comentario Adicional'
        ]);
    }

    function setupParents($tab) {
        CRUD::addField([
            'name'  => 'html_padres',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h2>Genarales de los padres<h2/>'
        ]);

        CRUD::addField([   // repeatable
            'name'  => 'parent_data',
            'label' => 'Padres y encargados',
            'type'  => 'repeatable',
            'tab' => $tab,
            'wrapper'   => [
                'id' => 'parent_data_table'
            ],
            'subfields' => [
                [
                    'name'    => 'family_type',
                    'type'    => 'radio',
                    'label'   => 'Parentesco',
                    'wrapper' => [
                        'class' => 'form-group col-md-12',
                        'id'=> 'family_type'
                    ],
                    'options' => [
                        'Padre' => 'Padre',
                        'Madre' => 'Madre',
                        'Encargado' => 'Encargado',
                    ],
                    'inline' => true,
                ],
                [
                    'name'    => 'parentesque_person',
                    'type'    => 'text',
                    'label'   => 'Parentesco del encargado',
                    'wrapper' => [
                        'class' => 'form-group col-md-12 d-none',
                        'id'=> 'parentesque_person'
                    ],
                ],
                [
                    'name'    => 'names',
                    'type'    => 'text',
                    'label'   => 'Nombre completo',
                    'wrapper' => ['class' => 'form-group col-md-8'],
                ],
                [
                    'name'    => 'dui',
                    'type'    => 'text',
                    'label'   => 'DUI',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'ocupation',
                    'type'    => 'text',
                    'label'   => 'Ocupación',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'work_place',
                    'type'    => 'text',
                    'label'   => 'Lugar de trabajo',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'position',
                    'type'    => 'text',
                    'label'   => 'Cargo',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'work_phone',
                    'type'    => 'phone',
                    'label'   => 'Teléfono de trabajo',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                    'config' => [
                        'onlyCountries' => ['sv'],
                        'initialCountry' => 'sv',
                    ]
                ],
                [
                    'name'    => 'cell_phone',
                    'type'    => 'phone',
                    'label'   => 'Teléfono celular',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                    'config' => [
                        'onlyCountries' => ['sv'],
                        'initialCountry' => 'sv',
                    ]
                ],
            ],
            // optional
            'new_item_label'  => 'Agregar', // customize the text of the button
            'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
        ]);

        CRUD::addField([
            'name'  => 'html_datos_del_padre',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h3>Datos de los padres<h3/>'
        ]);

        CRUD::addField([
            'name' => 'estado_civil',
            'label' => 'Estado Civil',
            'type' => 'radio',
            'options'     => [
                'Casados' => 'Casados',
                'Acompañados' => 'Acompañados',
                'Soltero' => 'Soltero',
                'Viudo' => 'Viudo'
            ],
            'inline' => true,
            'tab' => $tab,
        ]);

        CRUD::addField([
            'name' => 'fecha_del_matrimonio_civil',
            'type' => 'date_picker',
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
            'label' => 'Fecha del Matrimonio Civil'
        ]);

        CRUD::addField([
            'name' => 'fecha_del_matrimonio_religioso',
            'type' => 'date_picker',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Fecha del Matrimonio Religioso'
        ]);

        CRUD::addField([
            'name' => 'religion',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Religión'
        ]);

        CRUD::addField([
            'name' => 'estan_juntos',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
            'label' => 'Están Juntos?'
        ]);

        CRUD::addField([
            'name' => 'direccion_particular',
            'type' => 'textarea',
            'tab' => $tab,
            'label' => 'Dirección Particular'
        ]);

        CRUD::addField([
            'name' => 'telefono_de_casa',
            'type' => 'phone',
            'config' => [
                'onlyCountries' => ['sv'],
                'initialCountry' => 'sv',
            ],
            'tab' => $tab,
            'label' => 'Teléfono de casa'
        ]);

        CRUD::addField([
            'name' => 'casa',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Propia' => 'Propia',
                'Alquilada' => 'Alquilada',
            ],
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-4'],
            'label' => 'Casa'
        ]);

        CRUD::addField([
            'name' => 'remesas',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-4'],
            'label' => 'Remesas',
        ]);

        CRUD::addField([
            'name' => 'numero_de_personas_en_el_grupo_familiar',
            'type' => 'number',
            'attributes' => [
                'min' => 0
            ],
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-4'],
            'label' => 'Número de Personas en el Grupo Familiar'
        ]);
    }

    function setupOcupation($tab) {
        CRUD::addField([
            'name'  => 'html_ocupacion_padre',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h2>Ocupacion<h2/>'
        ]);

        CRUD::addField([
            'name' => 'ocupacion_del_padre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Ocupación del Padre'
        ]);

        CRUD::addField([
            'name' => 'lugar_de_trabajo_del_padre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Lugar de Trabajo del Padre'
        ]);

        CRUD::addField([
            'name' => 'cargo_del_padre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Cargo del Padre'
        ]);

        CRUD::addField([
            'name' => 'ocupacion_de_la_madre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Ocupación de la Madre'
        ]);

        CRUD::addField([
            'name' => 'lugar_de_trabajo_de_la_madre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Lugar de Trabajo de la Madre'
        ]);

        CRUD::addField([
            'name' => 'cargo_de_la_madre',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Cargo de la Madre'
        ]);
    }

    function setupDocuments($tab) {
        CRUD::addField([
            'name'  => 'html_documentos',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h2>Información adicional<h2/>'
        ]);

        CRUD::addField([
            'name' => 'documentos',
            'type' => 'select2_from_array',
            'label' => 'Documentos que presenta',
            'tab' => $tab,
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
            'allows_multiple' => true,
            'select_all' => true,
        ]);

        CRUD::addField([
            'name' => 'observaciones',
            'type' => 'textarea',
            'tab' => $tab,
            'label' => 'Observaciones'
        ]);
    }

    function setupObservation($tab) {
        CRUD::addField([
            'name'  => 'html_observaciones',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h2>Observaciones<h2/>'
        ]);

        CRUD::addField([
            'name' => 'observaciones',
            'type' => 'textarea',
            'tab' => $tab,
            'label' => 'Observaciones'
        ]);
    }

    function setupSigies($tab) {
        CRUD::addField([
            'name'  => 'sigies',
            'type'  => 'custom_html',
            'tab' => $tab,
            'value' => '<h2>SIGIES<h2/>'
        ]);

        CRUD::addField([
            'name' => 'nacionalidad',
            'type' => 'text',
            'tab' => $tab,
            'label' => 'Nacionalidad'
        ]);

        CRUD::addField([
            'name' => 'retornado',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'tab' => $tab,
            'label' => 'Retornado'
        ]);

        CRUD::addField([
            'name' => 'condicion_de_discapacidad',
            'type' => 'select2_from_array',
            'tab' => $tab,
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
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No',
                'No aplica' => 'No aplica'
            ],
            'tab' => $tab,
            'label' => 'Posee diagnóstico clínico'
        ]);

        CRUD::addField([
            'name' => 'estudiante_referido_a',
            'type' => 'select2_from_array',
            'label' => 'Estudiante referido a',
            'tab' => $tab,
            'options' => [
                'No aplica' => 'No aplica',
                'Docente Apoyo a la inclusión' => 'Docente Apoyo a la inclusión',
                'Centro de Orientación' => 'Centro de Orientación'
            ],
            'allows_multiple' => true
        ]);

        CRUD::addField([
            'name' => 'estudiante_recibe',
            'type' => 'select2_from_array',
            'label' => 'Estudiante recibe',
            'tab' => $tab,
            'options' => ['No aplica' => 'No aplica', 'Terapia de Rehabilitación' => 'Terapia de Rehabilitación', 'Atención Psiquiátrica' => 'Atención Psiquiátrica', 'Terapia de lenguaje' => 'Terapia de lenguaje', 'Fisioterapia' => 'Fisioterapia', 'Atención Neurológica' => 'Atención Neurológica', 'Terapia de Audición y Lenguaje' => 'Terapia de Audición y Lenguaje', 'Atención Psicológica' => 'Atención Psicológica', 'Otros' => 'Otros'],
            'allows_multiple' => true
        ]);

        CRUD::addField([
            'name' => 'correo_electronico_del_estudiante',
            'type' => 'email',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Correo Electrónico del Estudiante'
        ]);

        CRUD::addField([
            'name' => 'telefono_de_contacto_del_estudiante',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Teléfono de Contacto del Estudiante'
        ]);

        CRUD::addField([
            'name' => 'whatsapp',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'tab' => $tab,
            'label' => 'WhatsApp'
        ]);

        CRUD::addField([
            'name' => 'convivencia_familiar',
            'type' => 'enum',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Convivencia Familiar'
        ]);

        CRUD::addField([
            'name' => 'cantidad_de_personas_que_viven_con_el_estudiante',
            'type' => 'number',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'attributes' => [
                'min' => 0
            ],
            'tab' => $tab,
            'label' => 'Cantidad de Personas que Viven con el Estudiante'
        ]);

        CRUD::addField([
            'name' => 'tiene_acceso_a_internet',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Tiene Acceso a Internet'
        ]);

        CRUD::addField([
            'name' => 'tiene_internet_residencial',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Tiene Internet Residencial'
        ]);

        CRUD::addField([
            'name' => 'compañia_de_internet',
            'type' => 'text',
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
            'label' => 'Compañía de Internet'
        ]);

        CRUD::addField([
            'name' => 'puede_sintonizar_canal_10',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
            'label' => 'Puede Sintonizar Canal 10'
        ]);

        CRUD::addField([
            'name' => 'sintoniza_la_franja_educativa',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Sintoniza la Franja Educativa'
        ]);

        CRUD::addField([
            'name' => 'posee_computadora',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Si' => 'Si',
                'No' => 'No'
            ],
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => $tab,
            'label' => 'Posee Computadora'
        ]);

        CRUD::addField([
            'name' => 'recibira_sus_clases_de_forma',
            'type' => 'radio',
            'inline' => true,
            'options' => [
                'Presencial' => 'Presencial',
                'Virtual' => 'Virtual'
            ],
            'tab' => $tab,
            'label' => 'Recibirá sus Clases de Forma'
        ]);

        CRUD::addField([
            'name' => 'correo_electronico_del_padre_o_madre',
            'type' => 'email',
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
            'label' => 'Correo Electrónico del Padre o Madre'
        ]);

        CRUD::addField([
            'name' => 'ultimo_grado_de_escolaridad_del_padre_o_madre',
            'type' => 'enum',
            'tab' => $tab,
            'wrapper' => ['class' => 'form-group col-md-6'],
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
