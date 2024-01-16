<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
        .text-end{
            text-align: right!important;
        }
        .text-center{
            text-align: center!important;
        }
        .text-justify{
            text-align: justify!important;
        }
        .uppercase{
            text-transform: uppercase;
        }
        .underline{
            text-decoration: underline;
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
    @php
        $collect = collect($student->parent_data);

        $padre = $collect->where('family_type', 'Padre')->first();
        $madre = $collect->where('family_type', 'Madre')->first();
        $encargado = $collect->where('family_type', 'Encargado')->first();

    @endphp
    <div class="row mt-5" style="position: relative">
        <div class="col-md-4 col-lg-12" style="text-align: start">
            <h1 class="header-title mb-3">
                COLEGIO "PEDRO GEOFFROY RIVAS"
            </h1>
            <p>EL TREBOL, SANTA ANA</p>
        </div>
        <div class="col-md-4 col-lg-12" style="float: right">
            <img src="{{ asset($student?->foto) }}" alt="" height="100">
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12 col-lg-12">
            <h3 class="underline" style="text-align: center">FICHA DE MATRICULA {{ session('year')?->year }}</h3>
        </div>
    </div>
    <div class="row mt-3" style="display: flex; justify-content: space-between">
        <h4 class="underline">GENERALES DEL ALUMNO</h4>
        <p class="uppercase">NIE: {{ $student->NIE }}</p>
        <p class="uppercase">Grado a estudiar: {{ $student->course->name }}</p>
    </div>
    <div class="row mt-2">
        <p>Nombre según partida de nacimiento: {{ $student->full_name }}</p>
        <p>Edad: {{ $student->edad }}</p>
        <p>Lugar y fecha de nacimiento: {{ $student->lugar_y_fecha_de_nacimiento }}</p>
        <p>Institución donde estudió el año anterior: {{ $student->institucion_donde_estudio_anterior }}</p>
        <p>Enfermedad permanente: {{ $student->enfermedad_permanente }}</p>
        <p>Tipo de Sangre: {{ $student->tipo_de_sangre }}</p>
    </div>

    <div class="row mt-2">
        <h4 class="underline">A. GENERALES DE LOS PADRES</h4>
        <table border="0" style="width: 100%;">
            <tr>
                <td><p>Nombre del Padre: {{ $padre['names'] ?? '' }}</p></td>
                <td><p>DUI: {{ $padre['dui'] ?? '' }}</p></td>
            </tr>
            <tr>
                <td><p>Nombre de la Madre: {{ $madre?->names }}</p></td>
                <td><p>DUI: {{ $madre?->dui }}</p></td>
            </tr>
            <tr>
                <td><p>Estado civil: {{ $student?->estado_civil }}</p></td>
            </tr>
            <tr>
                <td><p>Fecha de Matrimonio Civil: {{ $student?->fecha_del_matrimonio_civil }}</p></td>
            </tr>
            <tr>
                <td><p>Religión: {{ $student?->religion }}</p></td>
            </tr>
            <tr>
                <td><p>Están juntos: {{ $student?->estan_juntos }}</p></td>
            </tr>
            <tr>
                <td><p>Dirección particular: {{ $student?->direccion_particular }}</p></td>
            </tr>
            <tr>
                <td><p>Teléfono de casa: {{ $student?->telefono_de_casa }}</p></td>
            </tr>
            <tr>
                <td><p>Casa propia: {{ $student?->casa }}</p></td>
            </tr>
            <tr>
                <td><p>Recibe remesas: {{ $student?->remesas }}</p></td>
            </tr>
            <tr>
                <td><p>Número de Personas del grupo familiar: {{ $student?->numero_de_personas_en_el_grupo_familiar }}</p></td>
            </tr>
        </table>
    </div>

    <div class="row mt-4">
        <h4 class="underline">B. OCUPACIÓN</h4>

        <table style="width: 100%;">
            <tr>
                <th>Ocupación del padre</th>
                <th>Lugar de trabajo</th>
                <th>Cargo</th>
            </tr>
            <tr>
                <td class="text-center">{{ $padre['ocupation'] ?? '' }}</td>
                <td class="text-center">{{ $padre['work_place'] ?? '' }}</td>
                <td class="text-center">{{ $padre['position'] ?? '' }}</td>
            </tr>
            <tr>
                <th>Ocupación de la madre</th>
                <th>Lugar de trabajo</th>
                <th>Cargo</th>
            </tr>
            <tr>
                <td class="text-center">{{ $madre['ocupation'] ?? '' }}</td>
                <td class="text-center">{{ $madre['work_place'] ?? '' }}</td>
                <td class="text-center">{{ $madre['position'] ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="row mt-4">
        <h4 class="underline">C. DOCUMENTOS QUE PRESENTA</h4>

        @php
            foreach ($student?->documentos as $key => $value) {
                echo '<p>'.$value.'</p>';
            }
        @endphp
    </div>

    <div class="row mt-4">
        <h4 class="underline">OBSERVACIONES</h4>

        <p>{{ $student?->observaciones }}</p>
    </div>
</body>
</html>
