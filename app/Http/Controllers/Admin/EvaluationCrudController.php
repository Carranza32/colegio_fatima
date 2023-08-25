<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EvaluationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EvaluationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Evaluation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evaluation');
        CRUD::setEntityNameStrings('evaluation', 'evaluations');
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
            'name' => 'subject.name',
            'type' => 'text',
            'label' => 'Asignatura'
        ]);

        CRUD::addColumn([
            'name' => 'course.name',
            'type' => 'text',
            'label' => 'Curso'
        ]);

        CRUD::addColumn([
            'name' => 'evaluation_date',
            'label' => 'Fecha de evaluación'
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
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EvaluationRequest::class);

        CRUD::addField([
            'name' => 'subject_id',
            'type' => 'select2',
            'label' => 'Asignatura',
            'entity' => 'subject',
            'attribute' => 'name',
            'model' => \App\Models\Subject::class
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
            'name' => 'evaluation_date',
            'type' => 'date',
            'label' => 'Fecha de evaluación'
        ]);

        CRUD::addField([
            'name' => 'order',
            'type' => 'number',
            'default' => ($this->crud->getModel()::orderByDesc('order')->first()->orden ?? 0) + 1,
        ]);

        CRUD::addField([
            'name' => 'is_active',
            'type' => 'switch',
            'label' => 'Estado',
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
