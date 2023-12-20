<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class StudentScoreController extends Controller
{
    public function index()
    {
        $params = [
            'cursos' => Course::all(),
            'asignaturas' => Subject::all(),
        ];

        if (backpack_user()?->hasRole(User::ROLE_DOCENTE)) {
            $params['cursos'] = Course::where('id', backpack_user()?->teacher?->course_id)->get();
        }

        if (request()->get('curso') != null && request()->get('asignatura') != null) {
            $curso_id = request()->get('curso');
            $asignatura_id = request()->get('asignatura');

            $notas = Score::with(['subject', 'course', 'student'])
                        ->where('course_id', $curso_id)->where('subject_id', $asignatura_id)
                        ->get()
                        ->groupBy(function($item) {
                            return $item->student;
                        });
            $students = Student::where('course_id', $curso_id)->get();

            $params['students'] = $students;
            $params['notas'] = $notas;
            $params['selected_asignatura'] = Subject::find($asignatura_id);
            $params['selected_curso'] = Course::find($curso_id);
            $params['asignaturas'] = Subject::where('course_id', $curso_id)->get();
            $params['cantidad_evaluaciones'] = session('period')?->evaluations ?? 0;
        }

        return view('admin.notas', $params);
    }

    public function asignaturaByCourse(Request $request)
    {
        $curso_id = request()->get('curso_id');
        $asignaturas = Subject::where('course_id', $curso_id)->get();

        return response()->json($asignaturas);
    }

    public function saveScores(Request $request)
    {
        try {
            $scores = json_decode($request->get('scores'), true);
            $scores = collect($scores);

            foreach ($scores as $nota) {
                if ($nota['nota'] != null && $nota['nota'] != "null") {
                    $score = Score::where('student_id', $nota['student_id'])
                                ->where('subject_id', $nota['subject_id'])
                                ->where('course_id', $nota['course_id'])
                                ->where('evaluation_number', $nota['index'])
                                ->first();

                    if (!$score) {
                        $score = new Score();
                        $score->student_id = $nota['student_id'];
                        $score->subject_id = $nota['subject_id'];
                        $score->course_id = $nota['course_id'];
                        $score->evaluation_number = $nota['index'];
                    }

                    $score->score = $nota['nota'];
                    $score->save();
                }
            }

            \Alert::success('Notas guardadas correctamente')->flash();

            return redirect()->back();
        } catch (\Throwable $th) {
            dd($th->getMessage());
            \Alert::error('Error al guardar las notas')->flash();

            return redirect()->back();
        }
    }
}
