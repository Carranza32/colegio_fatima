<?php

use App\Models\Score;
use Backpack\Settings\app\Models\Setting;

if ( ! function_exists('prep_url'))
{
    /**
     * Prep URL
     *
     * Simply adds the http:// part if no scheme is included
     *
     * @param   string  the URL
     * @return  string
     */
    function prep_url($str = '')
    {
        if ($str === 'http://' OR $str === '')
        {
            return '';
        }
        $url = parse_url($str);
        if ( ! $url OR ! isset($url['scheme']))
        {
            return 'http://'.$str;
        }
        return $str;
    }
}

if ( ! function_exists('getStudentAverageByPeriod'))
{

    function getStudentAverageByPeriod($student_id, $subject_id, $course_id)
    {
        try {
            $periodPruebas = getScoreByPeriodPruebas($student_id, $subject_id, $course_id);
            $periodActividades = getScoreByPeriodActividades($student_id, $subject_id, $course_id);

            $promedio = number_format($periodPruebas + $periodActividades);

            if ($promedio > 0) {
                return $promedio;
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if ( ! function_exists('getScoreByPeriodPruebas'))
{

    function getScoreByPeriodPruebas($student_id, $subject_id, $course_id, $period = null) : float
    {
        try {
            if ($period == null) {
                $period = session('period');
            }

            $scores = Score::withoutGlobalScopes(['current_period'])
                ->whereDate('date_scope', '>=', $period?->start_date)
                ->whereDate('date_scope', '<=', $period?->end_date)
                ->where('student_id', $student_id)
                ->where('subject_id', $subject_id)
                ->where('course_id', $course_id)
                ->where('group', 'pruebas')
                ->orderBy('evaluation_number', 'asc')
                ->get();

            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                $total += $score->score;
                $count++;
            }

            $promedio = round(number_format($total / $count, 2));

            if ($count > 0) {
                return $promedio * 0.30;
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if ( ! function_exists('getScoreByPeriodActividades'))
{

    function getScoreByPeriodActividades($student_id, $subject_id, $course_id, $period = null) : float
    {
        try {
            if ($period == null) {
                $period = session('period');
            }

            $scores = Score::withoutGlobalScopes(['current_period'])
                ->whereDate('date_scope', '>=', $period?->start_date)
                ->whereDate('date_scope', '<=', $period?->end_date)
                ->where('student_id', $student_id)
                ->where('subject_id', $subject_id)
                ->where('course_id', $course_id)
                ->where('group', 'actividades')
                ->orderBy('evaluation_number', 'asc')
                ->get();

            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                $total += $score->score;
                $count++;
            }

            $promedio = round(number_format($total / $count, 2));

            if ($count > 0) {
                return $promedio * 0.70;
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if ( ! function_exists('getStudentAverageByYear')){
    function getStudentAverageByYear($student, $subject_id) : float
    {
        try {
            $year = session('year')->year;

            $scores = Score::withoutGlobalScopes(['current_period'])
                ->where('student_id', $student->id)
                ->where('subject_id', $subject_id)
                ->where('course_id', $student->course->id)
                ->whereYear('date_scope', $year)
                ->orderBy('evaluation_number', 'asc')
                ->get();

            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                $total += $score->score;
                $count++;
            }

            if ($count > 0) {
                return round(number_format($total / $count, 2));
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if ( ! function_exists('getStudentAverageAccumulated')){
    function getStudentAverageAccumulated($student, $subject_id, $periodos) : float
    {
        try {
            $year = session('year')->year;

            $minDate = $periodos->min('start_date');
            $maxDate = $periodos->max('end_date');

            $scores = Score::withoutGlobalScopes(['current_period'])
                ->where('student_id', $student->id)
                ->where('subject_id', $subject_id)
                ->where('course_id', $student->course->id)
                ->whereDate('date_scope', '>=', $minDate)
                ->whereDate('date_scope', '<=', $maxDate)
                ->orderBy('evaluation_number', 'asc')
                ->get();

            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                $total += $score->score;
                $count++;
            }

            if ($count > 0) {
                return round(number_format($total / $count, 2));
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if ( ! function_exists('getScoreByPeriod'))
{

    function getScoreByPeriod($period, $student, $subject_id, $evaluation_number) : float
    {
        try {
            $scores = Score::withoutGlobalScopes(['current_period'])
                ->whereDate('date_scope', '>=', $period?->start_date)
                ->whereDate('date_scope', '<=', $period?->end_date)
                ->where('student_id', $student->id)
                ->where('subject_id', $subject_id)
                ->where('course_id', $student->course_id)
                ->where('evaluation_number', $evaluation_number)
                ->first();

            if ($scores) {
                return $scores->score;
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if (! function_exists('formatNumber')) {
    /**
     * Number format.
     *
     * @param $value amount
     */
    function formatNumber($value): string
    {
        if ($value === null) {
            return '';
        }

        $number = number_format(
            $value,
            Setting::get('decimals_digits'),
            Setting::get('dec_point'),
            Setting::get('thousands_sep'),
        );

        return $number;
    }
}

if (! function_exists('getStudentScore')) {
    /**
     * Get student score.
     *
     * @param $student_id
     * @param $subject_id
     * @param $course_id
     * @param $evaluation_number
     * @param $group
     */
    function getStudentScore($student_id, $subject_id, $course_id, $evaluation_number, $group, $period = null)
    {
        if ($period == null) {
            $period = session('period');
        }

        $score = Score::withoutGlobalScopes(['current_period'])
            ->whereDate('date_scope', '>=', $period?->start_date)
            ->whereDate('date_scope', '<=', $period?->end_date)
            ->where('student_id', $student_id)
            ->where('subject_id', $subject_id)
            ->where('course_id', $course_id)
            ->where('group', $group)
            ->where('evaluation_number', $evaluation_number)
            ->first();

        if ($score) {
            return $score->score;
        }

        return 0;
    }
}
