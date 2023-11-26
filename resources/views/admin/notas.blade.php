@extends(backpack_view('blank'))

@section('content')
    <div class="row mt-5">
        <div class="col-md-4 col-lg-12">
            <h1 class="header-title mb-3">
                Ingreso de notas
            </h1>
            <div class="card custom-card-shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Seleccione el curso y la asignatura, luego presione buscar.</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <form action="{{ route('notas-alumno') }}" method="get">
                                        <th scope="row">
                                            <div class="form-group">
                                                <label for="sel_curso">Curso</label>
                                                <select name="curso" class="form-control" id="sel_curso" aria-label="Select curso">
                                                    <option>Seleccione Curso</option>
                                                    @foreach ($cursos as $item)
                                                        <option value="{{ $item->id }}" {{ request()->get('curso') == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                                <label for="sel_curso">Asignatura</label>
                                                <select name="asignatura" class="form-control" id="sel_asignatura" aria-label="Select asignatura">
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

    @if (isset($evaluaciones))
        <div class="row">
            <div class="col-md-4 col-lg-12">
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
                                        <th scope="col" colspan="{{ count($evaluaciones) }}"><div class="text-center">Notas</div></th>
                                        <th scope="col" rowspan="2" colspan="1"><div class="text-center">Promedio</div></th>
                                    </tr>
                                    <tr>
                                        @foreach ($evaluaciones as $item)
                                            <th scope="col" class="text-center">N{{ ($loop->index + 1) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notas as $key => $item)
                                        @php
                                            $alumno = json_decode($key);

                                            $min_score =  \Backpack\Settings\app\Models\Setting::get('min_score');
                                            $max_score =  \Backpack\Settings\app\Models\Setting::get('max_score');
                                        @endphp
                                        @if ($alumno != null)
                                            <tr>
                                                <td>{{ $alumno?->NIE }}</td>
                                                <td>{{ $alumno?->full_name }}</td>
                                                {{-- <td>{{ $selected_asignatura?->nombre }}</td> --}}

                                                @foreach ($item as $nota)
                                                    <td><input class="form-control w-100" maxlength="2" min="0" max="10"  data-used="1" data-i="{{$loop->iteration}}" type="number" data-score="{{ $nota->id }}" name="notas[]" value="{{ $nota->score }}"></td>
                                                @endforeach

                                                <td><input class="form-control w-100" type="number" disabled value="{{ getStudentAverage($alumno->id, $selected_asignatura->id) }}"></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-lg btn-block">Guardar notas</button>
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
                        id: $(this).data('score'),
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
                        curso_id: $(this).val()
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
