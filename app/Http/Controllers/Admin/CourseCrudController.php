<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourseCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/course');
        CRUD::setEntityNameStrings('course', 'courses');
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
            'label' => 'Curso'
        ]);

        CRUD::addColumn([
            'name' => 'level',
            'type' => 'text',
            'label' => 'Nivel'
        ]);

        CRUD::addColumn([
            'name' => 'letter',
            'label' => 'letter'
        ]);

        CRUD::addColumn([
            'name' => 'order',
            'label' => 'Orden'
        ]);

        CRUD::addColumn([
            'name' => 'is_active',
            'type' => 'text',
            'label' => 'Estado'
        ]);

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::addFilter([
            'name' => 'name',
            'type' => 'select2',
            'label' => 'Nombre',
        ], function () {
            return $this->crud->getModel()::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        CRUD::addFilter([
            'name' => 'level',
            'type' => 'select2',
            'label' => 'Nivel',
        ], function () {
            return $this->crud->getModel()::all()->pluck('level', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        CRUD::addFilter([
            'name' => 'letter',
            'type' => 'select2',
            'label' => 'Letra',
        ], function () {
            return $this->crud->getModel()::all()->pluck('letter', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        CRUD::addFilter(
            [
                'name' => 'is_active',
                'type' => 'dropdown',
                'label' => "Estado",
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
        CRUD::setValidation(CourseRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'level',
            'label' => 'Nivel',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'letter',
            'label' => 'Letra',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'schedule_file',
            'type' => 'upload',
            'label' => 'Horario',
            'upload' => true,
            'disk' => 'public',
        ]);

        CRUD::addField([
            'name' => 'order',
            'type' => 'number',
            'default' => ($this->crud->getModel()::orderByDesc('order')->first()->orden ?? 0) + 1,
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
