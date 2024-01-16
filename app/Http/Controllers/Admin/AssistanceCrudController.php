<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AssistanceRequest;
use App\Models\Assistance;
use App\Models\AssistanceDetail;
use App\Models\AssistanceJustification;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Traits\CheckPermissionsCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    use CheckPermissionsCrud;

    private $disabled = null;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Assistance::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/assistance');
        CRUD::setEntityNameStrings('Asistencia', 'Asistencias');

        $this->crud->denyAccess('show');
        $this->crud->enableExportButtons();

        Assistance::saving(function($entry) {
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
            'name' => 'date',
            'label' => 'Fecha',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'course.name',
            'label' => 'Grado',
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
            'label' => 'Grado',
        ], function () {
            return $this->crud->getModel()::with('course')->get()->pluck('course.name', 'course.id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'course_id', $value);
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

        CRUD::addField([
            'name' => 'date',
            'label' => 'Fecha',
            'type' => 'date',
            'default' => date('Y-m-d'),
        ]);

        $cursos = Course::all()->pluck('name', 'id');

        if (backpack_user()?->hasRole(User::ROLE_DOCENTE)) {
            $cursos = Course::where('id', backpack_user()?->teacher?->course_id)->get()->pluck('name', 'id');
        }

        CRUD::addField([
            'name' => 'course_id',
            'label' => 'Grado',
            'type' => 'select2_from_array',
            'options' => $cursos,
            'attributes' => $this->disabled,
        ]);

        CRUD::addField([
            'name' => 'alumns_assistances',
            'type' => 'alumns_assistances',
            'entry' => $this->crud->getCurrentEntry() ?? null,
            'justifications' => AssistanceJustification::get(['id', 'name']),
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

    public function store()
    {
        $request = $this->crud->getRequest();
        $asistencias = $request->input('assistances');
        $observacion = $request->input('observacion');

        $request->merge([
            'observations' => $observacion,
        ]);

        $response = $this->traitStore();

        $entry = $this->crud->getCurrentEntry();

        foreach (json_decode($asistencias) as $value) {
            AssistanceDetail::create([
                'assistance_id' => $entry->id,
                'student_id' => $value->alumno_id,
                'has_assistance' => $value->asistencia,
                'justificacion_id' => $value->justificacion_id ?? '',
                'created_by' => backpack_user()?->id,
            ]);
        }

        return $response;
    }

    public function update()
    {
        $request = $this->crud->getRequest();
        $asistencias = $request->input('assistances');
        $observacion = $request->input('observacion');

        $entry = $this->crud->getCurrentEntry();

        $response = $this->traitUpdate();

        foreach (json_decode($asistencias) as $value) {
            AssistanceDetail::where('assistance_id', $entry->id)
                ->where('student_id', $value->alumno_id)
                ->update([
                    'has_assistance' => $value->asistencia,
                    'justificacion_id' => $value->justificacion_id ?? '',
                    'updated_by' => backpack_user()?->id,
                ]);
        }

        $entry->update([
            'updated_by' => backpack_user()?->id,
            'observacion' => $observacion,
        ]);

        return $response;
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
