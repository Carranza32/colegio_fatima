
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <style>
            .page_break {
                page-break-before: always;
            }
            * {
                font-family: sans-serif, Verdana, Arial;
            }
            .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
                color: #344767;
                font-weight: 400;
                line-height: 1.2;
                margin-bottom: 0.5rem;
                margin-top: 0;
            }
            table.table tfoot tr td{
                font-weight: bold;
                font-size: x-small;
            }
            table th{
                background-color: #fafafa;
            }
            hr{
                color: #1b3b70;
            }
            .gray {
                background-color: lightgray
            }
            .title{
                color: #1b3b70;
            }
            .title-logo{
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                flex-direction: row;
            }
            table.table {
                border-collapse: collapse;
                width: 100%;
                font-size: 11px !important;
            }

            .table {
                --bs-table-bg: transparent;
                --bs-table-accent-bg: transparent;
                --bs-table-striped-color: #67748e;
                --bs-table-striped-bg: rgba(0,0,0,.05);
                --bs-table-active-color: #67748e;
                --bs-table-active-bg: rgba(0,0,0,.1);
                --bs-table-hover-color: #67748e;
                --bs-table-hover-bg: rgba(0,0,0,.075);
                border-color: #e9ecef;
                color: #67748e;
                margin-bottom: 1rem;
                vertical-align: top;
                width: 100%;
                font-size: 11px !important;
            }
            .h3, h3 {
                font-size: 1.875rem;
                font-weight: 700;
                letter-spacing: -.05rem;
                line-height: 1.375;
            }
            .h4, h4 {
                font-weight: 700;
                letter-spacing: -.05rem;
                line-height: 1.375;
            }

            .table td, th {
                border: 1px solid #dddddd;
                text-align: center;
                padding: 8px;
            }
            .badge {
                margin: 0 0.2em;
            }
            .text-white {
                color: #fff!important;
            }
            .p-2 {
                padding: 0.5rem!important;
            }
            .rounded-pill {
                border-radius: 50rem!important;
            }
            .bg-primary {
                background-color: #7c69ef!important;
            }
            .badge {
                display: inline-block;
                padding: 0.25em 0.4em;
                margin: 0 0.2em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: 0.25rem;
                transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            }

            .alert-good{
                background-color: #e5fff0 !important;
                color: #1ed762 !important;
            }

            .alert-bad{
                background-color: #fee5e6 !important;
                color: #fb4819 !important;
            }
            .text-start{
                text-align: left!important;
            }
            .page_break {
                page-break-before: always;
            }
            .no-page_break {
                page-break-inside: avoid;
            }

            .no-page_break-2 {
                page-break-inside: auto;
            }
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
<body>
    <div class="row mt-5">
        <div class="col-md-4 col-lg-12">
            <div class="col-md-4 col-lg-12" style="text-align: center">
                <h1 class="header-title mb-3">
                    Informe de asistencia
                </h1>
            </div>
            <div class="col-md-4 col-lg-12" style="float: right">
                <img src="{{ asset('Logo Colegio Pedro Geoffroy Rivas.jpeg') }}" alt="" height="50">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-lg-12">
            <div class="card custom-card-shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @foreach ($courses as $course)
                                @if ($course->students != null && $course->students->count() > 0)
                                    @php
                                        $asistenciaIds = App\Models\Assistance::withoutGlobalScopes(['current_period'])
                                            ->where('course_id', $course->id)
                                            ->whereDate('date', '>=', $period?->start_date)
                                            ->whereDate('date', '<=', $period?->end_date)
                                            ->get()
                                            ->pluck('id');

                                        $total_asistencia = count($asistenciaIds);
                                        Carbon\Carbon::setLocale('es');

                                        $fecha_inicio = Carbon\Carbon::parse($period?->start_date)->format('d F Y');
                                        $fecha_fin = Carbon\Carbon::parse($period?->end_date)->format('d F Y');
                                    @endphp
                                    <div class="card custom-card-shadow">
                                        <h4>{{ $course->name }}</h4>
                                        <h5>Rango de fecha: {{$fecha_inicio}} - {{$fecha_fin}}</h5>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Alumno</th>
                                                            <th>N° de clases realizadas</th>
                                                            <th>N° de clases asistidas</th>
                                                            <th>N° de clases <b>no</b> asistidas</th>
                                                            <th>Porcentaje de Asistencia</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $students = [];

                                                            if ($alumnos == null || count($alumnos) == 0) {
                                                                $students = $course->students;
                                                            }else{
                                                                $students = $alumnos;
                                                            }
                                                        @endphp

                                                        @foreach ($students as $student)
                                                            @php
                                                                $total_asistidos =  App\Models\AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                                                                    ->where('student_id', $student?->id)
                                                                    ->where('has_assistance', 1)
                                                                    ->count();

                                                                $total_no_asistidos =  App\Models\AssistanceDetail::whereIn('assistance_id', $asistenciaIds)
                                                                    ->where('student_id', $student?->id)
                                                                    ->where('has_assistance', 0)
                                                                    ->count();

                                                                try {
                                                                    $porcentaje_asistencia = round($total_asistidos * 100 / $total_asistencia);
                                                                } catch (\Throwable $th) {
                                                                    $porcentaje_asistencia = 0;
                                                                }
                                                            @endphp

                                                            <tr>
                                                                <td>{{ $student->full_name }}</td>
                                                                <td>{{ $total_asistencia }}</td>
                                                                <td>{{ $total_asistidos }}</td>
                                                                <td>{{ $total_no_asistidos }}</td>
                                                                <td>{{ $porcentaje_asistencia }}%</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($show_details)
                                        <div class="card custom-card-shadow">
                                            <h4>Detalles</h4>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Fecha</th>
                                                                <th>Asistencia</th>
                                                                <th>Justificación</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $assistances = App\Models\Assistance::whereIn('id', $asistenciaIds)->get();
                                                            @endphp

                                                            @foreach ($assistances as $asis)
                                                                @php
                                                                    $student = $alumnos->first();
                                                                    $fecha = Carbon\Carbon::parse($asis->date)->format('d F Y');

                                                                    $asistencia_detail = App\Models\AssistanceDetail::with('justification')->where('assistance_id', $asis->id)
                                                                        ->where('student_id', $student?->id)
                                                                        ->first();

                                                                    $asistencia = $asistencia_detail?->has_assistance == 1 ? 'Si' : 'No';

                                                                    $justificacion = App\Models\AssistanceJustification::where('id', $asistencia_detail?->justificacion_id)->first();
                                                                @endphp

                                                                <tr>
                                                                    <td>{{ $fecha }}</td>
                                                                    <td>{{ $asistencia }}</td>
                                                                    <td>{{ $justificacion?->name }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div style="page-break-after:always;"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
