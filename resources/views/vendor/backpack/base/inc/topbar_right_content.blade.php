<!-- This file is used to store topbar (right) items -->
@php
    if (!session()->has('year')) {
        session(['year' => \App\Models\SchoolYear::where('year', \Backpack\Settings\app\Models\Setting::get('year_selected'))->first() ]);
    }

    if (!session()->has('period')) {
        session(['period' =>
            \App\Models\Period::where('fecha_inicio', '<=', date(session('year')?->year.'-m-d'))
            ->where('fecha_fin', '>=', date(session('year')?->year.'-m-d'))
            ->first()]);
    }

    $year = session('year');

    $period = session('period');
@endphp

<div class="btn-group">
    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        @php
            if( $period ){
                echo "{$period?->name}";
            }else{
                echo 'Ningún periodo seleccionado.';
            }
        @endphp
    </button>
    <ul class="dropdown-menu">
        @php
            $period = session('period');
            $year = session('year');

            if( $period && $year ){
                foreach (\App\Models\Period::getAllPeriodsByYear() as $period) {
                    $url = route('update.period.session', [$period?->id]);

                    echo "
                        <li>
                            <a class='dropdown-item' href='{$url}'>
                                {$period?->name}
                            </a>
                        </li>
                    ";
                }
            }
        @endphp
    </ul>
</div>
