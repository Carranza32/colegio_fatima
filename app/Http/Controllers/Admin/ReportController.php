<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\AssistanceDetail;
use App\Models\Course;
use App\Models\Period;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function notas(Request $request)
    {
        $params = [
            'alumnos' => Student::all(),
            'periodos' => Period::all(),
            'cursos' => Course::all(),
        ];

        return view('admin.reports.notas', $params);
    }

    public function asistencia(Request $request)
    {
        $params = [
            'alumnos' => Student::all(),
            'periodos' => Period::all(),
            'cursos' => Course::all(),
        ];

        return view('admin.reports.asistencia', $params);
    }

    public function downloadPeriodAsistReport(Request $request)
    {
        $alumno = Student::find($request->alumno_id);
        $period = Period::find($request->period_id);
        $curso_id = $alumno?->curso_id;
        $alumno_id = $alumno?->id;

        $asistenciaIds = Assistance::where('course_id', $curso_id)
            ->whereDate('date', '>=', $period?->start_date)
            ->whereDate('date', '<=', $period?->end_date)
            ->get()
            ->pluck('id');

        $total_asistencia = count($asistenciaIds);

        $total_asistidos = AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                    ->where('student_id', $alumno?->id)
                    ->where('has_assistance', 1)
                    ->count();

        try {
            $porcentaje_asistencia = round($total_asistidos * 100 / $total_asistencia);
        } catch (\Throwable $th) {
            $porcentaje_asistencia = 0;
        }

        $asistencias = Assistance::with('course', 'subject', 'assistance_detail.student')
                    ->whereHas('assistance_detail', function ($query) use ($alumno_id) {
                        $query->where('student_id', $alumno_id);
                    })
                    ->orderBy('date', 'asc')
                    ->get()
                    ->groupBy(function($item) {
                        return $item->subject;
                    });

        $params = [
            'asistencias' => $asistencias,
            'periodo' => $period,
            'alumno' => $alumno,
            'total_asistencia' => $total_asistencia,
            'total_asistidos' => $total_asistidos,
            'porcentaje_asistencia' => $porcentaje_asistencia,
            'director' => Setting::get('director_name') ?? '',
            'subjects' => Subject::all(),
            'student' => $alumno,
        ];



        // dd($notas);
        return view('admin.reports.reporte_asistencia', $params);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.periodo_asistencia_pdf', $params);

            //return view('admin.reports.anual_pdf', $params);

            return $pdf->download("{$alumno?->full_name_rut} {$period?->name}.pdf");

        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return $th->getMessage();
        }

        //return view('admin.reports.periodo_pdf', $params);
    }

    public function downloadPeriodScoreReport(Request $request)
    {
        $alumno = Student::find($request->alumno_id);

        $period = Period::find($request->period_id);

        $curso_id = $alumno?->course_id;

        $cantPromedios = 0;
        $total_asistencia = 0;
        $total_asistidos = 0;
        $porcentaje_asistencia = 0;

        $notas = Score::with('subject', 'course')
                    ->where('student_id', $alumno?->id)
                    ->where('course_id', $curso_id)
                    ->whereDate('date_scope', '>=', $period?->start_date)
                    ->whereDate('date_scope', '<=', $period?->end_date)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->subject;
                    });

        $max_evaluations = $period?->evaluations;

        $asistenciaIds = Assistance::where('course_id', $curso_id)
        ->whereDate('date', '>=', $period?->start_date)
        ->whereDate('date', '<=', $period?->end_date)
        ->get()
        ->pluck('id');

        $total_asistencia = count($asistenciaIds);

        $total_asistidos = AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                    ->where('student_id', $alumno?->id)
                    ->where('has_assistance', 1)
                    ->count();

        try {
            $porcentaje_asistencia = round($total_asistidos * 100 / $total_asistencia);
        } catch (\Throwable $th) {
            $porcentaje_asistencia = 0;
        }

        if ($notas) {
            $cantPromedios = count($notas);
        }

        $params = [
            'notas' => $notas,
            'maximo_notas' => $max_evaluations,
            'alumno' => $alumno,
            'cantPromedios' => $cantPromedios,
            'periodo' => $period,
            'total_asistencia' => $total_asistencia,
            'total_asistidos' => $total_asistidos,
            'porcentaje_asistencia' => $porcentaje_asistencia,
            'director' => Setting::get('director_name') ?? '',
        ];

        $asignaturas = [];

        foreach ($notas as $key => $item) {
            $asignaturas[] = json_decode($key);
        }

        // dd($asignaturas);

        try {
            // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.periodo_pdf', $params);

            return view('admin.reports.periodo_pdf', $params);

            return $pdf->download("{$alumno?->full_name_rut} {$period?->name}.pdf");

        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return $th->getMessage();
        }

        //return view('admin.reports.periodo_pdf', $params);
    }

    public function downloadYearScoreReport(Request $request)
    {
        // int $alumno_id = null, Array $periodIds = null
        $alumno = Student::find($request->alumno_id);

        $curso_id = $alumno?->course_id;

        $asignaturasIds = Score::with('subject', 'course')
                    ->where('student_id', $alumno?->id)
                    ->where('course_id', $curso_id)
                    ->get()
                    ->pluck('subject_id');

        $asignaturas = Subject::whereIn('id', $asignaturasIds)->get();
        $asignaturas = Subject::where('course_id', $curso_id)->get();

        $periods = Period::all();

        $anual = [];
        $total_asistencia = 0;
        $total_asistidos = 0;
        $porcentaje_asistencia = 0;

        foreach ($asignaturas as $asignatura) {
            $sum_period = 0;

            foreach ($periods as $period) {
                $promedio = Score::withoutGlobalScopes(['current_period'])
                                ->where('student_id', $alumno?->id)
                                ->where('subject_id', $asignatura?->id)
                                ->whereDate('date_scope', '>=', $period?->start_date)
                                ->whereDate('date_scope', '<=', $period?->end_date)
                                ->avg('score');

                // $detalleNota = 0;

                // for ($i=0; $i < $period->evaluations; $i++) {
                //     $detalleNota = Score::withoutGlobalScopes(['current_period'])
                //                 ->where('student_id', $alumno?->id)
                //                 ->where('subject_id', $asignatura?->id)
                //                 ->whereDate('date_scope', '>=', $period?->start_date)
                //                 ->whereDate('date_scope', '<=', $period?->end_date)
                //                 ->where('evaluation_number', $i + 1)
                //                 ->first();
                // }

                // $anual[$asignatura?->name][] = [
                //     'evaluacion' => $i + 1,
                //     'promedio' => round($promedio),
                //     'detalle' => ($detalleNota?->score ?? 0)
                // ];
                $anual[$asignatura?->name][] = $asignatura->id;
            }
        }

        $asistenciaIds = Assistance::withoutGlobalScopes(['current_period'])
                                    ->where('course_id', $curso_id)
                                    ->whereDate('date_scope', '>=', $period?->start_date)
                                    ->whereDate('date_scope', '<=', $period?->end_date)
                                    ->get()
                                    ->pluck('id');

        $total_asistencia = count($asistenciaIds);

        $total_asistidos = AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                                        ->where('student_id', $alumno?->id)
                                        ->where('has_assistance', 1)
                                        ->count();

        try {
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();
        }

        try {
            $porcentaje_asistencia = round($total_asistidos * 100 / $total_asistencia);
        } catch (\Throwable $th) {
            $porcentaje_asistencia = 0;
        }

        $params = [
            'alumno' => $alumno,
            'anual' => $anual,
            'periodos' => $periods,
            'total_asistencia' => $total_asistencia,
            'total_asistidos' => $total_asistidos,
            'porcentaje_asistencia' => $porcentaje_asistencia,
            'director' => Setting::get('director_name') ?? '',
        ];
        try {
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();
        }


        return view('admin.reports.anual_pdf', $params);
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.anual_pdf', $params);


            return $pdf->download("{$alumno?->full_name_rut} reporte anual.pdf");
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return $th->getMessage();
        }
    }

    public function alumnsByCourse(Request $request)
    {
        $curso_id = request()->get('curso_id');
        $data = Student::where('course_id', $curso_id)->get();

        return response()->json($data);
    }

    function asistanceExportIndex(Request $request) {
        $params = [
            'cursos' => Course::all(),
            'periodos' => Period::all(),
            'alumnos' => Student::all(),
        ];

        return view('admin.reports.asistencia_export', $params);
    }
}
