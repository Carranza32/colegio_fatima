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

if ( ! function_exists('getStudentAverage'))
{

    function getStudentAverage(int $student_id, $asignatura_id = null) : float
    {
        try {
            $scores = Score::where('student_id', $student_id)
                    ->where('subject_id', $asignatura_id)
                    ->whereHas('evaluation', function($query) {
                        $query->where('evaluations.deleted_at', null);
                    })
                    ->get();

            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                if ($score->score > 0) {
                    $total += $score->score;
                    $count++;
                }
            }

            if ($count > 0) {
                return round(number_format($total / $count, 2));
            } else {
                return 0;
            }
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
