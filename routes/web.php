<?php

use Illuminate\Support\Facades\Route;
use App\Models\Permission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(backpack_url());
});

Route::get('permisos', function () {
    $permissions = [
        ["name" => "setting",
        "description" => "Configuraciones"],

        ["name" => "log",
        "description" => "Logs"],

        ["name" => "users",
        "description" => "Usuarios"],

        ["name" => "roles",
        "description" => "Roles"],

        ["name" => "permissions",
        "description" => "Permisos"],

        ["name" => "period",
        "description" => "Periodos"],

        ["name" => "school-year",
        "description" => "Años escolares"],

        ["name" => "notas-alumno",
        "description" => "Notas de alumnos"],

        ["name" => "reporte-notas",
        "description" => "Reporte de notas"],

        ["name" => "reporte-asistencia",
        "description" => "Reporte de asistencia"],

        ["name" => "course",
        "description" => "Cursos"],

        ["name" => "student",
        "description" => "Estudiantes"],

        ["name" => "subject",
        "description" => "Materias"],

        ["name" => "teacher",
        "description" => "Profesores"],
    ];

    $types = ["read", "create", "update", "delete"];

    foreach ($permissions as $value) {
        foreach ($types as $type) {
            Permission::updateOrCreate([
                'name' => $value['name'] . "." . $type,
            ],[
                'name' => $value['name'] . "." . $type,
                'description' => $value['description'],
                'guard_name' => 'web',
            ]);
        }
    }
});
