<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolYearRequest;
use App\Models\Calendar;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SchoolYearCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SchoolYearCrudController extends CrudController
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
        CRUD::setModel(\App\Models\SchoolYear::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/school-year');
        CRUD::setEntityNameStrings('Años escolar', 'Años escolares');

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
            'name' => 'year',
            'label' => 'Año escolar',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'description',
            'label' => 'Descripción',
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
        CRUD::setValidation(SchoolYearRequest::class);

        CRUD::addField([
            'name' => 'year',
            'label' => 'Año escolar',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'description',
            'label' => 'Descripción',
            'type' => 'textarea',
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

        $year = $request->input('year');

        $date = Carbon::create($year, 1, 1);

        $startOfYear = $date->copy()->startOfYear();
        $endOfYear   = $date->copy()->endOfYear();

        $period = CarbonPeriod::create($startOfYear, $endOfYear);

        $datesRange = [];

        // Iterate over the period
        foreach ($period as $date) {
            if ($date->isSaturday() || $date->isSunday()) {
                $is_weekend = true;
                $is_available = true;
            }else{
                $is_weekend = false;
                $is_available = true;
            }

            //$datesRange[] = $date->format('Y-m-d');

            Calendar::create([
                'date' => $date->format('Y-m-d'),
                'is_weekend' => $is_weekend,
                'is_available' => $is_available,
            ]);
        }

        $response = $this->traitStore();

        return $response;
    }
}
