<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PeriodRequest;
use App\Models\Period;
use App\Models\SchoolYear;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class PeriodCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PeriodCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Period::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/period');
        CRUD::setEntityNameStrings('Periodo', 'Periodos');

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
        CRUD::column('name');
        CRUD::column('description');
        CRUD::column('start_date');
        CRUD::column('end_date');

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::addFilter([
            'name' => 'name',
            'type' => 'select2',
            'label' => 'Periodo',
        ], function () {
            return $this->crud->getModel()::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'start_date',
                'label' => "Fecha de Inicio",
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'start_date', '>=', $dates->from);
                $this->crud->addClause('where', 'start_date', '<=', $dates->to.' 23:59:59');
            }
        );

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'end_date',
                'label' => "Fecha de Fin",
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'end_date', '>=', $dates->from);
                $this->crud->addClause('where', 'end_date', '<=', $dates->to.' 23:59:59');
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
        CRUD::setValidation(PeriodRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'description',
            'label' => 'Descripción',
            'type' => 'textarea',
        ]);

        CRUD::addField([
            'name' => 'start_date',
            'label' => 'Fecha de Inicio',
            'type' => 'date_picker',
        ]);

        CRUD::addField([
            'name' => 'end_date',
            'label' => 'Fecha de Inicio',
            'type' => 'date_picker',
        ]);
    }

    public function store()
    {
        $request = $this->crud->getRequest();

        $start_date = $request->input('start_date');

        $response = $this->traitStore();

        return $response;
    }

    public function update()
    {
        $request = $this->crud->getRequest();

        $start_date = $request->input('start_date');

        $response = $this->traitUpdate();

        return $response;
    }

    public function updateYearPeriodSession(Request $request)
    {
        session(['year' => SchoolYear::find($request->year_selected) ]);

        $period = \App\Models\Period::find($request->periodo_id);

        session(['period' => $period]);

        return redirect()->back();
    }

    public function updateYearSession($year)
    {
        session(['year' => SchoolYear::find($year)]);

        return redirect()->back();
    }

    public function searchByYear(Request $request)
    {
        $schoolYear = SchoolYear::find($request->year);

        $periods = Period::withoutGlobalScopes(['current_year'])->whereYear('start_date', $schoolYear?->year)->get();

        return response()->json([
            'status' => true,
            'periods' => $periods,
        ], 200);
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
