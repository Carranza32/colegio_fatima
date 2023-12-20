@extends(backpack_view('blank'))

@section('content')
    <div class="row mt-5">
        <div class="col-md-4 col-lg-12">
            <h1 class="header-title mb-3">
                Exportar asistencia SIGIES
            </h1>
        </div>
    </div>
    <div class="row container-fluid">
        <div class="col-md-4 col-lg-12">
            <div class="card custom-card-shadow">
                <div class="card-body">
                    <form action="{{ route('assistance.import') }}" method="POST" id="report_form">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="dateFilter">Rango de fecha</label>
                            <input class="form-control" type="text" name="daterange" id="dateFilter" value="" required />
                        </div>

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
                            <select name="alumno_id" class="form-control select2" id="sel_alumno" required>
                                <option value="all">Todos</option>
                                @foreach ($alumnos as $item)
                                    <option value="{{ $item->id }}" @selected(request()->get('alumno_id') == $item->id) >{{ $item->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="sel_curso"></label>
                            <button type="submit" class="btn btn-primary" style="margin-top: 30px">.: Exportar :.</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('.select2').select2({
            placeholder: 'Seleccione un alumno',
            allowClear: true
        })

        var from_date;
        var to_date;
        var formated_from_date;
        var formated_to_date;

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
                        $('#sel_alumno').append(`
                            <option value="all">Todos</option>
                        `);

                        response?.forEach(el => {
                            $('#sel_alumno').append(`
                                <option value="${el?.id}">${el?.full_name}</option>
                            `)
                        })
                    }
                }
            })
        })

        $(document).ready(function() {
            $('#sel_curso').trigger('change')

            var datepicker = $('input[name="daterange"]').daterangepicker({
                showDropdowns: true,
                locale: {
                    format: 'DD/MM/YYYY',
                    cancelLabel: 'Limpiar'
                },
                startDate: moment(),
                endDate: moment(),
                ranges: {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Este mes': [moment().startOf('month'), moment().endOf('month')],
                    'Último mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Últimos 3 meses': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Últimos 6 meses': [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Últimos 12 meses': [moment().subtract(12, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                },
            });

            $('input[name="daterange"]').blur();

            datepicker.on('apply.daterangepicker', function(ev, picker) {
                from_date = picker.startDate.format('DD/MM/YYYY');
                to_date = picker.endDate.format('DD/MM/YYYY');
                formated_from_date = picker.startDate.format('YYYY-MM-DD');
                formated_to_date = picker.endDate.format('YYYY-MM-DD');

                $(this).val(from_date + ' - ' + to_date);
            });

            datepicker.on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                from_date = '';
                to_date = '';
                formated_from_date = '';
                formated_to_date = '';
            });
        })
    </script>
@endsection
