@extends(backpack_view('blank'))
@include('backpack.ui::inc.scripts')

@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <h1 class="header-title mb-3">
                Ingreso de notas
            </h1>
            <div class="card custom-card-shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Seleccione el grado y la asignatura, luego presione buscar.</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <form action="{{ route('notas-alumno') }}" method="get">
                                        <th scope="row">
                                            <div class="form-group">
                                                <label for="sel_curso">Grado</label>
                                                <select name="curso" class="form-control select2" id="sel_curso" aria-label="Select curso">
                                                    <option>Seleccione Grado</option>
                                                    @foreach ($cursos as $item)
                                                        <option value="{{ $item->id }}" {{ request()->get('curso') == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                                <label for="sel_curso">Asignatura</label>
                                                <select name="asignatura" class="form-control select2" id="sel_asignatura" aria-label="Select asignatura">
                                                    <option>Seleccione Asignatura</option>
                                                    @foreach ($asignaturas as $item)
                                                        <option value="{{ $item->id }}" {{ request()->get('asignatura') == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="form-group">
                                                <label for="sel_curso"></label>
                                                <button type="submit" class="btn btn-primary" style="margin-top: 30px">.: Buscar :.</button>
                                            </div>
                                        </th>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isset($students))
        <div class="row">
            <div class="col-lg-12">
                <form class="card custom-card-shadow" action="{{ route('notas-alumno.save') }}" method="POST" id="score-table">
                    <input type="hidden" name="scores">
                    @csrf
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabla-notas">
                                <thead>
                                    <tr>
                                        <th scope="col" rowspan="2">NIE</th>
                                        <th scope="col" rowspan="2">Alumno</th>
                                        {{-- <th scope="col" rowspan="2">Asignatura</th> --}}
                                        <th scope="col" colspan="{{ count($evaluacionesPruebas) + 1 }}"><div class="text-center">Prueba Objetiva (30%)</div></th>
                                        <th scope="col" colspan="{{ count($evaluacionesActividades) + 1 }}"><div class="text-center">Actividades (70%)</div></th>
                                        <th scope="col" rowspan="2" colspan="1"><div class="text-center">Promedio periodo</div></th>
                                    </tr>
                                    <tr>
                                        @foreach ($evaluacionesPruebas as $item)
                                            <th scope="col" class="text-center fw-light">{{ $item['name'] }}</th>

                                            @if ($loop->last)
                                                <th scope="col" class="text-center">Promedio x 30%</th>
                                            @endif
                                        @endforeach

                                        @foreach ($evaluacionesActividades as $item)
                                            <th scope="col" class="text-center fw-light">{{ $item['name'] }}</th>

                                            @if ($loop->last)
                                                <th scope="col" class="text-center">Promedio x 70%</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($students == null || $students->count() == 0)
                                        <tr>
                                            <td colspan="{{ $cantidad_evaluaciones + 3 }}" class="text-center">No hay alumnos registrados en este grado</td>
                                        </tr>
                                    @endif

                                    @foreach ($students as $student)
                                        @php
                                            $min_score =  \Backpack\Settings\app\Models\Setting::get('min_score');
                                            $max_score =  \Backpack\Settings\app\Models\Setting::get('max_score');
                                        @endphp
                                        @if ($student != null)
                                            <tr>
                                                <td>{{ $student?->NIE }}</td>
                                                <td>{{ $student?->full_name }}</td>

                                                @foreach ($evaluacionesPruebas as $item)
                                                    @php
                                                        $score = getStudentScore($student->id, $selected_asignatura->id, $selected_curso->id, $loop->index + 1, 'pruebas');

                                                        $index = $loop->index + 1;
                                                    @endphp

                                                    <td>
                                                        <input style="min-width: 80px;" type="number" name="notas[]" min="{{ $min_score }}" max="{{ $max_score }}" data-student_id="{{ $student->id }}" data-course_id="{{ $selected_curso->id }}" data-subject_id="{{ $selected_asignatura->id }}" data-index="{{ $index }}" data-group="pruebas" class="form-control w-100" value="{{ $score }}">
                                                    </td>

                                                    @if ($loop->last)
                                                        <td><input class="form-control w-100" type="number" disabled value="{{ getScoreByPeriodPruebas($student->id, $selected_asignatura->id, $selected_curso->id) }}"></td>
                                                    @endif
                                                @endforeach

                                                @foreach ($evaluacionesActividades as $item)
                                                    @php
                                                        $score = getStudentScore($student->id, $selected_asignatura->id, $selected_curso->id, $loop->index + 1, 'actividades');

                                                        $index = $loop->index + 1;
                                                    @endphp

                                                    <td>
                                                        <input style="min-width: 80px;" type="number" name="notas[]" min="{{ $min_score }}" max="{{ $max_score }}" data-student_id="{{ $student->id }}" data-course_id="{{ $selected_curso->id }}" data-subject_id="{{ $selected_asignatura->id }}" data-index="{{ $index }}" data-group="actividades" class="form-control w-100" value="{{ $score }}">
                                                    </td>

                                                    @if ($loop->last)
                                                        <td><input class="form-control w-100" type="number" disabled value="{{ getScoreByPeriodActividades($student->id, $selected_asignatura->id, $selected_curso->id) }}"></td>
                                                    @endif
                                                @endforeach

                                                <td><input class="form-control w-100" type="number" disabled value="{{ getStudentAverageByPeriod($student->id, $selected_asignatura->id, $selected_curso->id) }}"></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary btn-block">Guardar notas</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('after_styles')
    <style>
        input[type='number']{
            width: 40px;
        }
    </style>
@endsection

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script>
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es obligatorio.",
            range: jQuery.validator.format("Introduzca un valor entre {0} y {1}."),
            max: jQuery.validator.format("Por favor ingrese un valor menor o igual a {0}."),
            min: jQuery.validator.format("Por favor ingrese un valor mayor o igual a {0}.")
        });

        $(document).ready(function() {
            // $('#score-table').validate({
            //     rules: {
            //         'notas[]': {
            //             required: true,
            //             range: [0, 10]
            //         }
            //     },
            //     messages: {
            //         'notas[]': {
            //             range: "Por favor ingrese un valor entre 0 y 10."
            //         }
            //     }
            // });

            $('#score-table button').on('click', function(e) {
                var form = $(this);

                if (form.valid() === false) {
                    e.preventDefault();
                }

                var url = form.attr('action');
                var data = form.serialize();

                $('input[name="scores"]').val(JSON.stringify(getScores()));
                console.log($('input[name="scores"]').val());

                $('#score-table').submit();
            });

            function getScores() {
                var scores = [];

                $('#tabla-notas input[name="notas[]"]').each(function() {
                    scores.push({
                        student_id: $(this).data('student_id'),
                        course_id: $(this).data('course_id'),
                        subject_id: $(this).data('subject_id'),
                        group: $(this).data('group'),
                        index: $(this).data('index'),
                        nota: $(this).val()
                    });
                });

                return scores;
            }

            function habilitar(id){
                $("#"+id).removeAttr("disabled");
            }
            function deshabilitar(id){
                $("#"+id).attr("disabled", true);
            }

            $('#sel_curso').on('change', function () {
                $.ajax({
                    url: "{{ route('asignatura.by_course') }}",
                    method: 'POST',
                    data: {
                        curso_id: $(this).val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $('#sel_asignatura').empty()

                        if (response) {
                            response?.forEach(el => {
                                $('#sel_asignatura').append(`
                                    <option value="${el?.id}">${el?.name}</option>
                                `)
                            })
                        }
                    }
                })
            })
        });

        var times = 0;

        $('#tabla-notas input[type=number]').on('keypress', function(event){
            times ++;

            if (event.charCode >= 48 && event.charCode <= 57) {
                let next = $(this).parent().parent().next().find('input[type=number]');
                let i = $(this).data('i');
                let used = $(this).data('used')

                $(this).data('used', used + 1)

                if (next.length > 0) {
                    if (used > 1) {
                        setTimeout(function(){ next.get(i - 1).focus(); }, 400);
                        $(this).data('used', 1)
                    }
                }

            }
        })
    </script>
@endsection
