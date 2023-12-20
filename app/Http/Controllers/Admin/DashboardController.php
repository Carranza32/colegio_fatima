<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\AssistanceDetail;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function index(Request $request)
    {
        $start_date = session('period')?->start_date;
        $end_date = session('period')?->end_date;

        if ($request->get('daterange') != null) {
            $date_range = $request->get('daterange');
            $date_range = explode(' - ', $date_range);
            $start_date = Carbon::parse($date_range[0])->format('Y-m-d');
            $end_date = Carbon::parse($date_range[1])->format('Y-m-d');
        }

        $students = Student::join('courses', 'courses.id', '=', 'students.course_id')
            ->selectRaw('course_id, courses.name, count(*) as count')
            ->groupBy('students.course_id', 'courses.name')
            ->get();

        $alumnsByCourse = [
            'labels' => $students->pluck('name')->toArray(),
            'values' => $students->pluck('count')->toArray(),
        ];

        $total_assistances = Assistance::with('assistance_detail')->whereBetween('date', [$start_date, $end_date])->get()->map(function ($assistance) {
            return $assistance->assistances;
        })->sum();

        $total_absences = Assistance::with('assistance_detail')->whereBetween('date', [$start_date, $end_date])->get()->map(function ($assistance) {
            return $assistance->absences;
        })->sum();

        $params = [
            'students_count' => Student::count(),
            'teachers_count' => Teacher::count(),
            'assisted_count' => $total_assistances,
            'absences_count' => $total_absences,
            'alumnsByCourse' => $alumnsByCourse,
            'asistenciasByMonth' => $this->getAsistenciasByMonth($start_date, $end_date, 1),
            'ausenciasByMonth' => $this->getAsistenciasByMonth($start_date, $end_date, 0),
        ];

        return view('admin.dashboard', $params);
    }

    function getAsistenciasByMonth($start_date, $end_date, $has_assistance) {
        $asistenciasByMonth = Assistance::join('assistance_details', 'assistance_details.assistance_id', '=', 'assistances.id')
            ->selectRaw('MONTH(date) as month, count(*) as count')
            ->whereBetween('date', [$start_date, $end_date])
            ->where('has_assistance', $has_assistance)
            ->groupBy('month')
            ->get();

        return [
            'labels' => collect($asistenciasByMonth->pluck('month')->toArray())->map(function ($mes) {
                return $this->meses[$mes - 1];
            }),

            'values' => collect($asistenciasByMonth->pluck('count')->toArray())->map(function ($monto) {
                return formatNumber($monto);
            }),
        ];
    }
}
