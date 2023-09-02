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
