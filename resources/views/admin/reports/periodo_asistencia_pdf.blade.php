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
                <img src="https://colegionovaduc.cl/wp-content/uploads/2022/05/LOGO-NOVADUC-ACTUAL-R-XXsmall-COLEGIO.png" alt="" height="50">
            </div>
        </div>
    </div>

    @if (isset($asistencias))
        <div class="row">
            <div class="col-md-4 col-lg-12">
                <div class="card custom-card-shadow">
                    <div class="card shadow-none border-0 mb-0">
                        <div class="card-body">
                            <p>Alumno: <strong> {{ $alumno?->full_name }}</strong></p>
                            <p>Curso: <strong>{{ $alumno?->curso?->nombre }}</strong></p>
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

                        {{-- INICIO NUEVO BLOQUE --}}


                        @if (isset($asistencias))
                        {{-- @dump($asistencia_view) --}}
                            <div class="row">
                                <div class="col-md-4 col-lg-12">
                                    <div class="card custom-card-shadow">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover" id="tabla-notas">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Fecha</th>
            
                                                            @foreach ($asistencia_header as $key => $item)
                                                                <th class="text-center">{{ $item->nombre }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($asistencia_view as $key => $item_asis)

                                                            @if ($loop->first)
                                                                <tr>
                                                                    <td>{{ $item_asis->fecha }}</td>
                                                                    @if ($item_asis->has_assistance == 1)
                                                                        <td class="text-center">
                                                                            *<i class="las la-check-circle" style="color: green"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if ($item_asis->has_assistance === 0)
                                                                        <td class="text-center">
                                                                            X<i class="lar la-times-circle" style="color: red"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if (is_null($item_asis->has_assistance))
                                                                        <td class="text-center"></td>
                                                                    @endif
                                                            @endif
            
                                                            @if ($loop->iteration != 1)
                                                                @if ($item_asis->fecha != $asistencia_view[$key - 1]->fecha)
                                                                    <tr>
                                                                    <td>{{ $item_asis->fecha }}</td>
                                                                    @if ($item_asis->has_assistance == 1)
                                                                        <td class="text-center">
                                                                            *<i class="las la-check-circle" style="color: green"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if ($item_asis->has_assistance === 0)
                                                                        <td class="text-center">
                                                                            X<i class="lar la-times-circle" style="color: red"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if (is_null($item_asis->has_assistance))
                                                                        <td class="text-center"></td>
                                                                    @endif
                                                                @endif
            
                                                                @if ($item_asis->fecha == $asistencia_view[$key - 1]->fecha)
                                                                    @if ($item_asis->has_assistance == 1)
                                                                        <td class="text-center">
                                                                            *<i class="las la-check-circle" style="color: green"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if ($item_asis->has_assistance === 0)
                                                                        <td class="text-center">
                                                                            X<i class="lar la-times-circle" style="color: red"></i>
                                                                        </td>
                                                                    @endif
            
                                                                    @if (is_null($item_asis->has_assistance))
                                                                        <td class="text-center"></td>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- FIN NUEVO BLOQUE --}}

                        <div class="row md-5" style="position: relative">
                            <div class="col-md-4" style="text-align: center">
                                <div style="margin-top: 3rem; text-align: center;">
                                    <p style="font-size: 13px !important; text-transform: uppercase; border-bottom: 2px solid #000; margin-left: 25%; margin-right: 25%">{{ $director }}</p>
                                    <h5>Firma Director(a)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
