<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\CheckPermissionsCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAccountMail;

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
    use CheckPermissionsCrud;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Teacher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/teacher');
        CRUD::setEntityNameStrings('Profesor', 'Profesores');

        $this->crud->denyAccess('show');
        $this->crud->enableExportButtons();

        //Enviar correo al crear un profesor
        Teacher::creating(function($entry) {
            $user = User::where('email', $entry->email)->first();
            $password = Str::random(8);

            if ($user == null) {
                $user = new User;
                $user->password = bcrypt( $password );
            }

            $user->name = $entry->name." ".$entry->last_name;
            $user->email = $entry->email;
            $user->save();

            $entry->user_id = $user->id;

            $user->assignRole(User::ROLE_DOCENTE);

            $emailData = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $password
            ];

            Mail::to($user->email)->cc([Setting::get('copy_email')])->send(new NewAccountMail($emailData));
        });

        Teacher::saving(function($entry) {
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

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::addFilter(
            [
            'name' => 'is_active',
            'type' => 'dropdown',
            'label' => 'Estado',
        ],
            [
            0 => 'Inactivo',
            1 => 'Activo',
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
            'name' => 'course_id',
            'type' => 'select2',
            'label' => 'Grado',
            'entity' => 'course',
            'attribute' => 'name_letter',
            'model' => \App\Models\Course::class
        ]);

        CRUD::addField([
            'name' => 'dui',
            'type' => 'text',
            'label' => 'DUI'
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Correo'
        ]);

        CRUD::addField([
            'name' => 'phone',
            'type' => 'phone',
            'label' => 'Teléfono',
            'config' => [
                'onlyCountries' => ['sv'],
                'initialCountry' => 'sv', // this needs to be in the allowed country list, either in `onlyCountries` or NOT in `excludeCountries`
                'separateDialCode' => true,
                'nationalMode' => true,
                'autoHideDialCode' => false,
                'placeholderNumberType' => 'MOBILE',
            ]
        ]);

        CRUD::addField([
            'name' => 'address',
            'type' => 'textarea',
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
