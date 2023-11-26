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

    public function downloadPeriodScoreReport(Request $request)
    {
        $alumno = Student::find($request->alumno_id);

        $period = Period::find($request->period_id);

        $curso_id = $alumno?->course_id;

        $cantPromedios = 0;
        $total_asistencia = 0;
        $total_asistidos = 0;
        $porcentaje_asistencia = 0;

        $notas = Score::with('subject', 'course', 'evaluation')
                    ->where('student_id', $alumno?->id)
                    ->where('course_id', $curso_id)
                    ->whereDate('date_scope', '>=', $period?->fecha_inicio)
                    ->whereDate('date_scope', '<=', $period?->fecha_fin)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->asignatura;
                    });

        $max_evaluations = DB::select( DB::raw("SELECT COUNT(*) AS total FROM scores WHERE student_id = :id GROUP BY subject_id ORDER BY total DESC limit 1"), [
            'id' => $alumno?->id,
        ]);

        $asistenciaIds = Assistance::where('course_id', $curso_id)
        ->whereDate('date', '>=', $period?->fecha_inicio)
        ->whereDate('date', '<=', $period?->fecha_fin)
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
            'maximo_notas' => ($max_evaluations != null) ? $max_evaluations[0]->total : 0,
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

        // dd($notas);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.periodo_pdf', $params);

            // return view('admin.reports.periodo_pdf', $params);

            return $pdf->download("{$alumno?->full_name_rut} {$period?->name}.pdf");

        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return $th->getMessage();
        }

        //return view('admin.reports.periodo_pdf', $params);
    }

    public function downloadYearScoreReport(Request $request)
    {
        try {
            // int $alumno_id = null, Array $periodIds = null
            $alumno = Student::find($request->alumno_id);

            $curso_id = $alumno?->course_id;

            $asignaturasIds = Score::with('subject', 'course', 'evaluation')
                        ->where('student_id', $alumno?->id)
                        ->where('course_id', $curso_id)
                        ->get()
                        ->pluck('subject_id');

            $asignaturas = Subject::whereIn('id', $asignaturasIds)->get();

            $periods = Period::all();

            $anual = [];
            $total_asistencia = 0;
            $total_asistidos = 0;
            $porcentaje_asistencia = 0;

            foreach ($asignaturas as $asignatura) {
                $sum_period = 0;

                foreach ($periods as $period) {
                    $promedio = Score::withoutGlobalScopes(['CurrentPeriod'])
                                    ->where('student_id', $alumno?->id)
                                    ->where('subject_id', $asignatura?->id)
                                    ->whereDate('date', '>=', $period?->fecha_inicio)
                                    ->whereDate('date', '<=', $period?->fecha_fin)
                                    ->avg('nota');

                    $anual[$asignatura?->nombre][] = round($promedio);
                }
            }

            $asistenciaIds = Assistance::withoutGlobalScopes(['CurrentPeriod'])
                                        ->where('course_id', $curso_id)
                                        ->whereDate('date', '>=', $period?->fecha_inicio)
                                        ->whereDate('date', '<=', $period?->fecha_fin)
                                        ->get()
                                        ->pluck('id');

            $total_asistencia = count($asistenciaIds);

            $total_asistidos = AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                                            ->where('student_id', $alumno?->id)
                                            ->where('has_assistance', 1)
                                            ->count();
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();
        }

        try {
            $porcentaje_asistencia = round($total_asistidos * 100 / $total_asistencia);
        } catch (\Throwable $th) {
            $porcentaje_asistencia = 0;
        }

        try {
            $params = [
                'alumno' => $alumno,
                'anual' => $anual,
                'periodos' => $periods,
                'total_asistencia' => $total_asistencia,
                'total_asistidos' => $total_asistidos,
                'porcentaje_asistencia' => $porcentaje_asistencia,
                'director' => Setting::get('director_name') ?? '',
            ];
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();
        }

        //
        try {
            // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.anual_pdf', $params);

            return view('admin.reports.anual_pdf', $params);

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
}
