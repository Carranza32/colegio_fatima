<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AssistanceRequest;
use App\Models\Assistance;
use App\Models\AssistanceDetail;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class AssistanceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AssistanceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $disabled = null;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Assistance::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/assistance');
        CRUD::setEntityNameStrings('Asistencia', 'Asistencias');

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
            'name' => 'date',
            'label' => 'Fecha',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'course.name',
            'label' => 'Curso',
        ]);

        CRUD::addColumn([
            'name' => 'subject.name',
            'label' => 'Asignatura',
        ]);

        CRUD::addColumn([
            'name' => 'assistances',
            'label' => 'Asistencias',
        ]);

        CRUD::addColumn([
            'name' => 'absences',
            'label' => 'Ausencias',
        ]);

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'date',
                'label' => "Fecha",
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'date', '>=', $dates->from);
                $this->crud->addClause('where', 'date', '<=', $dates->to.' 23:59:59');
            }
        );

        CRUD::addFilter([
            'name' => 'course_id',
            'type' => 'select2',
            'label' => 'Curso',
        ], function () {
            return $this->crud->getModel()::with('course')->get()->pluck('course.name', 'course.id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'course_id', $value);
        });

        CRUD::addFilter([
            'name' => 'asignatura',
            'type' => 'select2',
            'label' => 'Asignatura',
        ], function () {
            return $this->crud->getModel()->whereHas('subject')->get()->pluck('subject.name', 'subject.id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'subject_id', $value);
        });


    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AssistanceRequest::class);

        $attr_date = ['min' => date('Y-m-d')];

        if ($this->crud->getCurrentEntry()) {
            if ($this->crud->getCurrentEntry()?->created_by != backpack_user()?->id) {
                if (!backpack_user()->hasAnyRole([User::ROLE_ADMIN, User::SUPERGOD_ROLE])) {
                    $this->disabled = ['disabled' => 'disabled'];
                    $attr_date = [
                        'disabled' => 'disabled',
                        'min' => date('Y-m-d'),
                    ];
                }
            }
        }

        if (backpack_user()->hasAnyRole([User::ROLE_ADMIN, User::SUPERGOD_ROLE])) {
            $attr_date = [
                'min' => null,
            ];
        }

        CRUD::addField([
            'name' => 'date',
            'label' => 'Fecha',
            'type' => 'date',
            'default' => date('Y-m-d'),
            'attributes' => $attr_date,
        ]);

        CRUD::addField([
            'name' => 'course_id',
            'type' => 'select2_from_array',
            'options' => Course::all()->pluck('name', 'id'),
            'attributes' => $this->disabled,
        ]);

        CRUD::addField([
            'name' => 'subject_id',
            'type' => 'select2_from_array',
            'options' => Subject::pluck('name', 'id'),
            'attributes' => $this->disabled,
        ]);

        CRUD::addField([
            'name' => 'alumns_assistances',
            'type' => 'alumns_assistances',
            'entry' => $this->crud->getCurrentEntry() ?? null,
            'disabled' => ($this->disabled == null) ? null : 'disabled',
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

    public function getAlumnsByCourse(Request $request)
    {
        $courseId = $request->get('course_id');

        $alumns = Student::where('course_id', $courseId)->get();

        $asignaturas = Subject::where('course_id', $courseId)->get();

        if ($request->entry_id != null) {
            $details = AssistanceDetail::with('student')->where('assistance_id', $request->entry_id)->get();

            return response()->json([
                'message' => 'Alumnos obtenidos correctamente',
                'data' => $details,
                'edit' => true,
            ]);
        }

        return response()->json([
            'message' => 'Alumnos obtenidos correctamente',
            'data' => [
                'students' => $alumns,
                'asignaturas' => $asignaturas,
            ],
            'edit' => false,
        ]);
    }

    public function destroy($id)
    {
        $entry = Assistance::find($id);

        if ($entry != null) {
            if ($entry?->created_by == backpack_user()?->id) {
                if (!backpack_user()->hasAnyRole([User::ROLE_ADMIN, User::SUPERGOD_ROLE])) {
                    $this->crud->hasAccessOrFail('delete');

                    return $this->crud->delete($id);
                }
            }
        }
    }
}
