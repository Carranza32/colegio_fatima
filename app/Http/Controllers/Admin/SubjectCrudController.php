<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubjectRequest;
use App\Traits\CheckPermissionsCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubjectCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Subject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subject');
        CRUD::setEntityNameStrings('Materia', 'Materias');

        $this->crud->denyAccess('show');
        $this->crud->enableExportButtons();

        \App\Models\Subject::saving(function($entry) {
            if ($entry->created_by == null) {
                $entry->created_by = backpack_user()->id;
            }

            if ($entry->date_scope == null) {
                $entry->date_scope = date(session('year')?->year.'-m-d');
            }

            $entry->updated_by = backpack_user()->id;
        });
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
            'name' => 'name',
            'type' => 'text',
            'label' => 'Asignatura'
        ]);

        // CRUD::addColumn([
        //     'name' => 'course.name',
        //     'type' => 'text',
        //     'label' => 'Grado'
        // ]);

        CRUD::addColumn([
            'name' => 'status_description',
            'label' => 'Estado',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Activo') {
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
        // CRUD::addFilter([
        //     'name' => 'course_id',
        //     'type' => 'select2',
        //     'label' => 'Grado',
        // ], function () {
        //     return $this->crud->getModel()::with('course')->get()->pluck('course.name', 'course.id')->toArray();
        // }, function ($value) {
        //     $this->crud->addClause('where', 'course_id', $value);
        // });

        CRUD::addFilter(
            [
            'name' => 'is_active',
            'type' => 'dropdown',
            'label' => 'Estado',
        ],
            [
            0 => 'Inactivo',
            1 => 'Activo',
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
        CRUD::setValidation(SubjectRequest::class);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Asignatura'
        ]);

        // CRUD::addField([
        //     'name' => 'course_id',
        //     'type' => 'select2',
        //     'label' => 'Grado',
        //     'entity' => 'course',
        //     'attribute' => 'name_letter',
        //     'model' => \App\Models\Course::class
        // ]);

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
