@extends(backpack_view('blank'))

@push('after_styles')
<link rel="stylesheet" href="{{ asset('css/custom_cards.css') }}">
@endpush

@section('content')
    <header>

    </header>

    <h4>Seleccione el año y periodo de su preferencia</h4>

    <form class="my-2 my-lg-0" method="get" action="{{ route('update.year.period.session') }}">
        @csrf
        <div class="row">
            <div class="col-sm-4 col-md-3">
                <div class="form-group">
                    <label>Año escolar</label>
                    <select name="year_selected" class="form-control" id="year_selected" aria-label="Select periodo">
                        @foreach (\App\Models\SchoolYear::all() as $item)
                            <option value="{{ $item->id }}" @selected(session('year')?->id == $item?->id )>{{ $item?->year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="form-group">
                    <label>Periodos</label></br>
                    <select name="periodo_id" class="form-control" id="periodo_id" aria-label="Select periodo">
                        @foreach (\App\Models\Period::getAllPeriodsByYear() as $item)
                            <option value="{{ $item->id }}" @selected(session('period')?->id == $item->id )>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div style="margin-top: 30px;">
                    <button class="btn btn-success my-sm-0" type="submit">Seleccionar</button>
                </div>
            </div>
        </div>
    </form>

    <form class="row mt-5" method="get" action="{{ backpack_url('dashboard') }}" id="filter_date">
        <div class="col-3">
            <div class="form-group">
                <label class="form-label">Filtro por fecha</label><br>
                <input type="text" class="form-control rounded-pill" name="daterange"/>
            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            <div style="margin-top: 30px;">
                <button class="btn btn-outline-danger my-sm-0 btn-sm" id="clear_button" type="button">Limpiar <i class="las la-times-circle"></i></button>
            </div>
        </div>
    </form>

    <div class="row mt-5">
        <div class="col-3">
            <div class="card outlined-card shadow-none p-3">
                <h5>Total estudiantes</h5>
                <h3>{{ $students_count }}</h3>
            </div>
        </div>
        <div class="col-3">
            <div class="card outlined-card shadow-none p-3">
                <h5>Total presentes</h5>
                <h3>{{ $assisted_count }}</h3>
            </div>
        </div>
        <div class="col-3">
            <div class="card outlined-card shadow-none p-3">
                <h5>Total ausentes</h5>
                <h3>{{ $absences_count }}</h3>
            </div>
        </div>
        <div class="col-3">
            <div class="card outlined-card shadow-none p-3">
                <h5>Total docentes</h5>
                <h3>{{ $teachers_count }}</h3>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-6">
            <div class="card outlined-card custom-shadow p-3">
                <h4>Matriculas por Grado</h4>
                <div class="w-100" id="alumnsByCourseChart"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="card outlined-card custom-shadow p-3">
                <h4>Asistencias por mes</h4>
                <div class="w-100" id="doubleChart"></div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.com/libraries/jquery.mask"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        var inputRange = $('input[name="daterange"]')
        var date = "{{ request()->get('daterange') }}"
        var date_array = date.split(' - ')

        var date_start = date_array[0]
        var date_end = date_array[1]

        if (date_start && date_end) {
            inputRange.val(date_start + ' - ' + date_end)
        }

        inputRange.daterangepicker({
            ranges: {
                'Este año': [moment().startOf('year'), moment().endOf('year')],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Esta semana': [moment().startOf('week'), moment().endOf('week')],
                'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'La semana pasada': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
            },
        });

        inputRange.on('apply.daterangepicker', function (ev, picker) {
            $('#filter_date').submit()
        });

        $('#clear_button').on('click', function () {
            inputRange.val('')
            $('#filter_date').submit()
        })

        $(document).ready(function () {
            $('body').append($('#selectAlum'))

            var alumnsByCourse = @json($alumnsByCourse);
            var asistenciasByMonth = @json($asistenciasByMonth);
            var ausenciasByMonth = @json($ausenciasByMonth);

            initCharts(alumnsByCourse)
            doubleChart(asistenciasByMonth, ausenciasByMonth)

            $('#year_selected').on('change', function () {
                $.ajax({
                    url: "{{ route('periods.byYear') }}",
                    method: 'POST',
                    data: {
                        year: $(this).val()
                    },
                    success: function (response) {
                        $('#periodo_id').empty()

                        response?.periods.forEach(periodo => {
                            $('#periodo_id').append(`
                                <option value="${periodo?.id}">${periodo?.name}</option>
                            `)
                        })
                    }
                })
            })
        })

        function initCharts(alums_data) {
            var anio = "{{ session('year')?->year }}";

            var alumnsByCourse = {
                series: [{
                    name: 'Alumnos',
                    data: alums_data?.values
                }],
                labels: alums_data?.labels,
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                title: {
                    text: 'Cantidad de matriculas por Grado, '+anio,
                    floating: true,
                    // offsetY: 330,
                    align: 'bottom',
                    style: {
                        color: '#444'
                    }
                }
            };

            var alumnsByCourseChart = new ApexCharts(document.querySelector("#alumnsByCourseChart"), alumnsByCourse);
            alumnsByCourseChart.render();
        }

        function doubleChart(asistenciasByMonth, ausenciasByMonth) {
            var options = {
                series: [{
                    name: "Asistencias",
                    data: asistenciasByMonth?.values
                }, {
                    name: "Ausencias",
                    data: ausenciasByMonth?.values
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                stroke: {
                    // width: [0, 4],
                    curve: 'smooth'
                },
                title: {
                    text: 'Asistencias vs Ausencias',
                    align: 'left'
                },
                legend: {
                    tooltipHoverFormatter: function(val, opts) {
                        return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
                    }
                },
                tooltip: {
                    fillSeriesColor: false,
                    onDatasetHover: {
                        highlightDataSeries: false,
                    },
                    theme: 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter',
                    },
                },
                xaxis: {
                    categories: asistenciasByMonth?.labels,
                },
                yaxis: [
                    {
                        title: {
                            text: "Asistencias",
                        },
                    },
                    {
                        opposite: true,
                        title: {
                            text: "Ausencias",
                        },
                    },
                ],
            };

            var chart = new ApexCharts(document.querySelector("#doubleChart"), options);
            chart.render();
        }

        function ingresosChart(paymentsByMonth) {
            var options = {
                series: [{
                    name: "Ingresos",
                    data: paymentsByMonth?.values
                }],
                dataLabels: {
                    enabled: true,
                },
                chart: {
                    height: 350,
                    stacked: false,
                    type: 'area',
                },
                title: {
                    text: 'Ingresos',
                    align: 'left'
                },
                tooltip: {
                    fillSeriesColor: false,
                    onDatasetHover: {
                        highlightDataSeries: false,
                    },
                    theme: 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter',
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: paymentsByMonth?.labels,
                },
            };

            var chart = new ApexCharts(document.querySelector("#ingresosChart"), options);
            chart.render();
        }
    </script>
@endpush
