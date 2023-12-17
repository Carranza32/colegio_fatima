
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
                <h4>{{ $periodo->name }}</h4>
            </div>
            <div class="col-md-4 col-lg-12" style="float: right">
                <img src="{{ asset('Logo Colegio Pedro Geoffroy Rivas.jpeg') }}" alt="" height="50">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-lg-12">
            <div class="card custom-card-shadow">
                <div class="card shadow-none border-0 mb-0">
                    <div class="card-body">
                        <p>Alumno: <strong> {{ $alumno?->full_name }}</strong></p>
                        <p>Curso: <strong>{{ $alumno?->course?->name }}</strong></p>
                        <p>Año escolar: <strong>{{ session('year')?->year }}</strong></p>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <th colspan="3" class="text-center">Asistencia</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">N° de clases realizadas: {{ $total_asistencia }}</td>
                                <td class="text-center">N° de clases asistidas: {{ $total_asistidos }}</td>
                                <td class="text-center">Porcentaje de Asistencia: {{ $porcentaje_asistencia }}%</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-4 col-lg-12">
                            <div class="card custom-card-shadow">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>

                                                    @foreach($subjects as $subject)
                                                        <th>{{ $subject->name }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $periodDates = App\Models\Assistance::where('course_id', $student->course_id)
                                                        ->whereDate('date', '>=', $periodo->start_date)
                                                        ->whereDate('date', '<=', $periodo->end_date)
                                                        ->pluck('date')
                                                        ->unique();
                                                @endphp

                                                @foreach($periodDates as $date)
                                                    <tr>
                                                        <th class="text-center">{{ Carbon\Carbon::parse($date)->format('d M Y') }}</th>

                                                        @foreach($subjects as $subject)
                                                            <td class="text-center">
                                                                {{-- Verificar si el estudiante asistió a la materia en la fecha --}}
                                                                @php
                                                                    $attendance = $student->getAttendances($date, $subject->id);
                                                                @endphp

                                                                @if ($attendance != null)
                                                                    @if($attendance->has_assistance == 1)
                                                                        <span style="color: green;">X</span>
                                                                    @else
                                                                        <span style="color: red;">-</span>
                                                                    @endif
                                                                @else
                                                                    <span style="color: grey;"></span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
