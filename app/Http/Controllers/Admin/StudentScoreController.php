<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\Score;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentScoreController extends Controller
{
    public function index()
    {
        $params = [
            'cursos' => Course::all(),
            'asignaturas' => Subject::all(),
        ];

        if (request()->get('curso') != null && request()->get('asignatura') != null) {
            $curso_id = request()->get('curso');
            $asignatura_id = request()->get('asignatura');

            $notas = Score::with(['subject', 'course', 'student', 'evaluation'])
                        ->whereHas('evaluation', function($query) {
                            $query->where('evaluations.deleted_at', null);
                        })
                        ->where('course_id', $curso_id)->where('subject_id', $asignatura_id)
                        ->get()
                        ->groupBy(function($item) {
                            return $item->student;
                        });

            $evaluaciones = Evaluation::where('subject_id', $asignatura_id)->get();

            $params['notas'] = $notas;
            $params['evaluaciones'] = $evaluaciones;
            $params['selected_asignatura'] = Subject::find($asignatura_id);
            $params['asignaturas'] = Subject::where('course_id', $curso_id)->get();
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
                    $score = Score::find($nota['id']);
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
