<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ConductRecordRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ConductRecordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ConductRecordCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ConductRecord::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/conduct-record');
        CRUD::setEntityNameStrings('Registro de conducta', 'Registros de conducta');

        $this->crud->denyAccess('show');
        $this->crud->enableExportButtons();

        \App\Models\ConductRecord::saving(function($entry) {
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
            'label' => 'Nombre'
        ]);

        CRUD::addColumn([
            'name' => 'discipline_action',
            'type' => 'closure',
            'label' => 'Acción disciplinaria asociada a',
            'function' => function($entry) {
                if ($entry->action == 'Estudiantes') {
                    return $entry->student->full_name;
                }

                return $entry->teacher->full_name;
            }
        ]);

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
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ConductRecordRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text'
        ]);

        CRUD::addField([
            'name' => 'action',
            'label' => 'Acción disciplinaria asociada a:',
            'type' => 'radio',
            'options' => [
                'Estudiantes' => 'Estudiante',
                'Docentes' => 'Doctente',
            ],
            'default' => 'Estudiantes',
            'wrapper' => [
                'id' => 'action',
            ]
        ]);

        CRUD::addField([
            'name' => 'course_id',
            'label' => 'Filtrar por Grado',
            'type' => 'select2',
            'entity' => 'course',
            'wrapper' => [
                'id' => 'course_id',
            ]
        ]);

        CRUD::addField([
            'name' => 'student_id',
            'label' => 'Estudiante',
            'type' => 'select2',
            'entity' => 'student',
            'attribute' => 'full_name',
            'wrapper' => [
                'id' => 'student_id',
            ]
        ]);

        CRUD::addField([
            'name' => 'teacher_id',
            'label' => 'Docente',
            'type' => 'select2',
            'entity' => 'teacher',
            'attribute' => 'full_name',
            'wrapper' => [
                'id' => 'teacher_id',
            ]
        ]);

        CRUD::addField([
            'name' => 'description',
            'label' => 'Descripción',
            'type' => 'wysiwyg',
            'wrapper' => [
                'style' => 'height: 200px;'
            ]
        ]);

        CRUD::addField([
            'name' => 'is_active',
            'label' => 'Activo',
            'type' => 'switch',
            'default' => 1
        ]);

        CRUD::addField([
            'name' => 'custom_scripts',
            'type' => 'conductRecord.custom_scripts',
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
