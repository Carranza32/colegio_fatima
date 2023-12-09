<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CalendarRequest;
use App\Traits\CheckPermissionsCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CalendarCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CalendarCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Calendar::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/calendar');
        CRUD::setEntityNameStrings('calendar', 'calendars');

        $this->crud->denyAccess(['show', 'delete', 'update', 'create']);
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
        $this->crud->removeAllButtons();

        CRUD::addColumn([
            'name' => 'date',
            'label' => 'Fecha',
            'type' => 'date',
            'format' => 'dddd, MMMM D, YYYY',
        ]);

        CRUD::addColumn([
            'name' => 'is_available',
            'label' => 'Dia habil',
            'type' => 'editable_switch',
            'color'   => 'success',
            'onLabel' => '✓',
            'offLabel' => '✕',
        ]);

        CRUD::addColumn([
            'name' => 'is_weekend',
            'label' => 'Fin de semana',
            'type' => 'editable_switch',
            'color'   => 'success',
            'onLabel' => '✓',
            'offLabel' => '✕',
        ]);

        CRUD::addColumn([
            'name' => 'description',
            'label' => 'Descripción',
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

        CRUD::addFilter(
            [
                'name' => 'is_available',
                'type' => 'dropdown',
                'label' => "Día hábil",
            ],
            [
                0 => __('No'),
                1 => __('Si'),
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_available', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_weekend',
                'type' => 'dropdown',
                'label' => "Fin de semana",
            ],
            [
                0 => __('No'),
                1 => __('Si'),
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_weekend', $value);
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
        CRUD::setValidation(CalendarRequest::class);
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
