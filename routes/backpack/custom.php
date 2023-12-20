<?php

use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('course', 'CourseCrudController');
    Route::crud('student', 'StudentCrudController');
    Route::crud('subject', 'SubjectCrudController');
    Route::crud('teacher', 'TeacherCrudController');
    Route::crud('evaluation', 'EvaluationCrudController');
    Route::crud('assistance', 'AssistanceCrudController');
    Route::post('obtener-alumnos', 'AssistanceCrudController@getAlumnsByCourse')->name('obtener.alumnos');
    Route::crud('period', 'PeriodCrudController');
    Route::crud('school-year', 'SchoolYearCrudController');
    Route::crud('calendar', 'CalendarCrudController');
    Route::crud('inscription', 'InscriptionCrudController');
    Route::get('update/year/period/session', 'PeriodCrudController@updateYearPeriodSession')->name('update.year.period.session');
    Route::get('update/period/session/{id?}', 'PeriodCrudController@updatePeriodSession')->name('update.period.session');
    Route::get('update/year/session/{id?}', 'PeriodCrudController@updateYearSession')->name('update.year.session');
    Route::post('period/searchByYear', 'PeriodCrudController@searchByYear')->name('periods.byYear');

    //Notas
    Route::get('notas-alumno', 'StudentScoreController@index')->name('notas-alumno');
    Route::post('asignatura-by-course', 'StudentScoreController@asignaturaByCourse')->name('asignatura.by_course');
    Route::post('notas-alumno-save', 'StudentScoreController@saveScores')->name('notas-alumno.save');

    //Reportes
    Route::controller(ReportController::class)->prefix('reporte')->group(function () {
        Route::get('/notas', 'notas')->name('reporte.notas');

        //Asistencia
        Route::get('/asistencia', 'asistencia')->name('reporte.asistencia');
        Route::post('/downloadPeriodAsistReport', 'downloadPeriodAsistReport')->name('reporte.asistencia_period.download');


        Route::post('/generateReport', 'generateReport')->name('reporte.generate');

        Route::post('/downloadYearScoreReport', 'downloadYearScoreReport')->name('reporte.notas_year.download');
        Route::post('/downloadPeriodScoreReport', 'downloadPeriodScoreReport')->name('reporte.notas_period.download');

        Route::post('alumns-course', 'alumnsByCourse')->name('alumns.by_course');

        Route::get('asistance-export', 'asistanceExportIndex')->name('assistance.import.index');
        Route::post('asistance-export-download', 'asistanceExport')->name('assistance.import');
    });

    //Dashboard
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
}); // this should be the absolute last line of this file
