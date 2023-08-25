<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TeacherCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeacherCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Teacher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/teacher');
        CRUD::setEntityNameStrings('teacher', 'teachers');
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
            'name' => 'dui',
            'type' => 'text',
            'label' => 'DUI'
        ]);

        CRUD::addColumn([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Correo'
        ]);

        CRUD::addColumn([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Teléfono'
        ]);

        CRUD::addColumn([
            'name' => 'address',
            'type' => 'text',
            'label' => 'Dirección'
        ]);

        CRUD::addColumn([
            'name' => 'is_active',
            'type' => 'text',
            'label' => 'Estado'
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TeacherRequest::class);

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
            'name' => 'dui',
            'type' => 'text',
            'label' => 'DUI'
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Correo'
        ]);

        CRUD::addField([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Teléfono'
        ]);

        CRUD::addField([
            'name' => 'address',
            'type' => 'text',
            'label' => 'Dirección'
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
