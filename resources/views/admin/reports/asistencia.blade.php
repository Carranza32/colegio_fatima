@extends(backpack_view('blank'))

@section('content')
    <div class="row mt-5">
        <div class="col-md-4 col-lg-12">
            <h1 class="header-title mb-3">
                Informe de asistencia
            </h1>
        </div>
    </div>
    <div class="row container-fluid">
        <div class="col-md-4 col-lg-12">
            <div class="card custom-card-shadow">
                <div class="card-body">
                    <form action="{{ route('reporte.asistencia_period.download') }}" method="POST" id="report_form">
                        @csrf
                        <div class="form-group">
                            <label for="sel_curso">Curso</label>
                            <select name="curso" class="form-control" id="sel_curso" aria-label="Select curso" required>
                                @foreach ($cursos as $item)
                                    <option value="{{ $item->id }}" {{ request()->get('curso') == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sel_alumno">Alumno</label>
                            <select name="alumno_id" class="form-control" id="sel_alumno" required>
                                @foreach ($alumnos as $item)
                                    <option value="{{ $item->id }}" @selected(request()->get('alumno_id') == $item->id) >{{ $item->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!--label>Informe anual</label>
                        <br>
                        <label class="switch">
                            <input class="assistance_checkbox" type="checkbox" name="is_yearly" value="0">
                            <span class="slider round"></span>
                        </label>

                        <hr-->

                        <div class="form-group" id="toggle_periods">
                            <label for="sel_alumno">Periodos</label>
                            <select name="period_id" class="form-control" id="sel_period" required>
                                @foreach ($periodos as $item)
                                    <option value="{{ $item->id }}" @selected(session('period')?->id == $item->id) >{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sel_curso"></label>
                            <button type="submit" class="btn btn-primary" style="margin-top: 30px">.: Generar Informe :.</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<style>
    .main > .container-fluid {
        padding: 0 0 !important;
    }
    .main.pt-2 {
        padding: 0 0 !important;
    }
    .switch {
          position: relative;
          display: inline-block;
          width: 50px;
          height: 24px;
        }

        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }

        .slider:before {
          position: absolute;
          content: "";
          height: 16px;
          width: 16px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
</style>
@endsection

@section('after_scripts')
    <script>
        $('#sel_curso').on('change', function () {
            $.ajax({
                url: "{{ route('alumns.by_course') }}",
                method: 'POST',
                data: {
                    curso_id: $(this).val()
                },
                success: function (response) {
                    $('#sel_alumno').empty()

                    if (response) {
                        response?.forEach(el => {
                            $('#sel_alumno').append(`
                                <option value="${el?.id}">${el?.full_name}</option>
                            `)
                        })
                    }
                }
            })
        })

        $('input[name="is_yearly"]').on('change', function () {
            if ($(this).is(':checked')) {
                $('#toggle_periods').hide()
                $(this).val(1)

                let year_url = "{{ route('reporte.notas_year.download') }}"

                $('#report_form').attr('action', year_url)
            } else {
                $('#toggle_periods').show()
                $(this).val(0)

                let period_url = "{{ route('reporte.notas_period.download') }}"
                $('#report_form').attr('action', period_url)
            }
        })

        $(document).ready(function() {
            $('#sel_curso').trigger('change')
        })
    </script>
@endsection
