<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssistanceExport;
use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\AssistanceDetail;
use App\Models\Course;
use App\Models\Period;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Dompdf\CanvasFactory;

class ReportController extends Controller
{
    public function notas(Request $request)
    {
        $params = [
            'alumnos' => Student::all(),
            'periodos' => Period::all(),
            'cursos' => Course::all(),
        ];

        if (backpack_user()?->hasRole(User::ROLE_DOCENTE)) {
            $params['cursos'] = Course::where('id', backpack_user()?->teacher?->course_id)->get();
        }

        return view('admin.reports.notas', $params);
    }

    public function asistencia(Request $request)
    {
        $params = [
            'alumnos' => Student::all(),
            'periodos' => Period::all(),
            'cursos' => Course::all(),
        ];

        if (backpack_user()?->hasRole(User::ROLE_DOCENTE)) {
            $params['cursos'] = Course::where('id', backpack_user()?->teacher?->course_id)->get();
        }

        return view('admin.reports.asistencia', $params);
    }

    public function downloadPeriodAsistReport(Request $request)
    {
        $alumnos = [];

        if ($request->curso == "all") {
            $courses = Course::all();

            return $this->generateGlobalAsistReport($courses, $request->period_id);
        }

        if ($request->alumno_id == "all") {
            $courses = Course::whereIn('id', [$request->curso])->get();

            return $this->generateGlobalAsistReport($courses, $request->period_id);
        }else{
            $courses = Course::whereIn('id', [$request->curso])->get();
            $alumnos = Student::where('id', $request->alumno_id)->get();

            return $this->generateGlobalAsistReport($courses, $request->period_id, $alumnos);
        }

        $dompdf = new Dompdf();

        foreach ($alumnos as $alumno) {
            $this->generateAsistReportPdf($dompdf, $alumno, $request->period_id);
            $dompdf->getCanvas()->newPage();
        }

        $dompdf->stream("Informe de Asistencia.pdf");
    }

    function generateAsistReportPdf($pdf, Student $alumno, $periodId) {
        $period = Period::find($periodId);
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

        try {
            $pdf->loadView('admin.reports.reporte_asistencia', $params);
            $pdf->stream("{$alumno?->full_name} Asistencia {$period?->name}.pdf");

        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();
            return $th->getMessage();
        }
    }

    function generateGlobalAsistReport($courses, $periodId, $alumnos = null) {
        $period = Period::find($periodId);

        $params = [
            'courses' => $courses,
            'period' => $period,
            'alumnos' => $alumnos,
            'show_details' => $alumnos == null ? false : true,
        ];

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.globalAsistReport_pdf', $params);

            return $pdf->download("Informe de Asistencia.pdf");
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return $th->getMessage();
        }
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
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.periodo_pdf', $params);

            // return view('admin.reports.periodo_pdf', $params);

            return $pdf->download("{$alumno?->full_name} {$period?->name}.pdf");

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
        $periods = [];

        if ($request->period_id == null) {
            $periods = Period::all();
        }else {
            $periods = Period::whereIn('id', $request->period_id)->get();
        }

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

        // return view('admin.reports.anual_pdf', $params);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.anual_pdf', $params);

            return $pdf->download("{$alumno?->full_name} reporte anual.pdf");
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

        if (backpack_user()?->hasRole(User::ROLE_DOCENTE)) {
            $params['cursos'] = Course::where('id', backpack_user()?->teacher?->course_id)->get();
        }

        return view('admin.reports.asistencia_export', $params);
    }

    function asistanceExport(Request $request) {
        // Obtén los datos que deseas exportar
        // $query = Assistance::withoutGlobalScopes(['current_year', 'current_period'])
        //             ->with('course', 'subject', 'assistance_detail.student')
        //             ->where('course_id', $request->curso);

        $query = DB::table('assistances as asis')
                    ->select('asis.date', 'students.name as student_name', 'students.nie', 'details.has_assistance', 'assistance_justifications.name as justification_name', 'asis.observacion')
                    ->join('assistance_details as details', 'details.assistance_id', '=', 'asis.id')
                    ->join('courses', 'asis.course_id', '=', 'courses.id')
                    ->join('students', 'details.student_id', '=', 'students.id')
                    ->leftJoin('assistance_justifications', 'details.justificacion_id', '=', 'assistance_justifications.id');

        // if ($request->alumno_id != null && $request->alumno_id != '' && $request->alumno_id != 'all') {
        //     $query->whereHas('assistance_detail', function ($query) use ($request) {
        //         $query->where('student_id', $request->alumno_id);
        //     });
        // }

        if ($request->get('daterange') != null) {
            $startString = explode(' - ', $request->get("daterange"))[0];
            $endString = explode(' - ', $request->get("daterange"))[1];

            $start = Carbon::createFromFormat('d/m/Y', $startString)->format('Y-m-d');
            $end = Carbon::createFromFormat('d/m/Y', $endString)->format('Y-m-d');

            $query->where('date', '>=', $start.' 00:00:00')->where('date', '<=', $end.' 23:59:59');
        }

        //grouping
        $data = $query->orderBy('date', 'asc')->get();

        // Exporta los datos a Excel usando el paquete maatwebsite/excel
        return Excel::download(new AssistanceExport($data), 'plantilla_control_asistencia.xlsx');
    }

    function fichaMatricula(Request $request) {
        $student = Student::find($request->id);

        $params = [
            'student' => $student,
        ];

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.ficha_matricula', $params);

            return $pdf->download("Matricula {$student?->full_name}.pdf");
        } catch (\Throwable $th) {
            \Alert::add('error', 'Error al generar el archivo')->flash();

            return redirect()->back();
        }
    }
}
