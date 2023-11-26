<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationRequest;
use App\Models\Score;
use App\Models\Student;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
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
        CRUD::setEntityNameStrings('Evaluacion', 'Evaluaciones');

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
            'name' => 'evaluation_dates',
            'type' => 'date_range',
            'label' => 'Fecha de evaluación',
        ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'evaluation_date', '>=', $dates->from);
                $this->crud->addClause('where', 'evaluation_date', '<=', $dates->to . ' 23:59:59');
            }
        );

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
            'type' => 'date_picker',
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

    public function store()
    {
        $request = $this->crud->getRequest();

        $asignatura_id = $request->input('subject_id');
        $curso_id = $request->input('course_id');

        $response = $this->traitStore();

        $entry = $this->crud->getCurrentEntry();

        Student::where('course_id', $entry->course_id)->get()->each(function ($alumno) use ($entry, $asignatura_id) {
            Score::create([
                'evaluation_id' => $entry->id,
                'student_id' => $alumno->id,
                'course_id' => $entry->course_id,
                'subject_id' => $asignatura_id,
                'score' => 0,
            ]);
        });

        return $response;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $response = $this->crud->delete($id);

        Score::where('evaluation_id', $id)->delete();

        return $response;
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
