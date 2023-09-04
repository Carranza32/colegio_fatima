@php
    $entry = $field['entry'];
    $disabled = $field['disabled'] ?? '';
@endphp

@include('crud::fields.inc.wrapper_start')
<input type="hidden" value="[]" name="assistances">

<div class="col-md-12 mb-4">
    <div class="row mt-3 mb-2s">
        <div class="col">
            <h4>Asistencia</h4>
        </div>
    </div>
    <input type="hidden" id="fixed_operation_table_hidden" name="fixed_operation_table_hidden">

    {{-- Tabla --}}
    <div class="card border-0 shadow-none">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <table id="table-assistance" style="width: 100%;" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2">
                        <thead>
                            <th>Alumno</th>
                            <th class="text-center">Asistencia</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('crud::fields.inc.wrapper_end')

@section('after_styles')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
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

        input.assistance_checkbox:disabled ~ .slider.round {
            cursor: not-allowed;
        }
    </style>
@endsection

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.17/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        var assistances = $('input[name=assistances');
        var can_edit = "{{ $disabled }}";

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        jQuery(document).ready(function($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            $('select[name=course_id]').on('change', function(){
                const selected = $(this).val()

                console.log(selected);

                if (selected) {
                    $.ajax({
                        url: "{{ route('obtener.alumnos') }}",
                        type: 'POST',
                        data: {
                            course_id: selected,
                            entry_id: "{{ $entry->id ?? null }}",
                        },
                        success: function(result) {
                            $('#table-assistance tbody').empty();

                            if (result.edit) {
                                console.log(result.data);
                                result.data.forEach(element => {
                                    let checked = ''

                                    if (element?.has_assistance == 1) {
                                        checked = 'checked'
                                    }

                                    $('#table-assistance tbody').append(`
                                        <tr>
                                            <td>${element?.student?.full_name}</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input class="assistance_checkbox" type="checkbox" data-alumno="${element?.student?.id}" ${checked} ${can_edit}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    `);
                                });
                            } else {

                                result.data?.students.forEach(element => {
                                    $('#table-assistance tbody').append(`
                                        <tr>
                                            <td>${element?.full_name}</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input class="assistance_checkbox" type="checkbox" data-alumno="${element?.id}" checked sabled}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    `);
                                });

                                $('select[name=subject_id]').empty()

                                result?.data?.asignaturas?.forEach(element => {
                                    const json = JSON.stringify(element)

                                    $('select[name=subject_id]').append(`<option value='${element?.id}'>${element?.name}</option>`)
                                });
                            }

                            Toast.fire({
                                icon: 'success',
                                title: result.message
                            })
                        },
                        error: function(result) {
                            // Show an alert with the result
                            console.log(result)
                        }
                    });
                }
            })

            $('form').submit(function(){
                var assits = []

                $('#table-assistance tbody input.assistance_checkbox').each(function(){
                    assits.push({
                        alumno_id: $(this).data('alumno'),
                        asistencia: $(this).is(':checked') ? 1 : 0,
                    });
                });

                assistances.val( JSON.stringify(assits) )
            })

            $('select[name=course_id]').trigger('change');
        })

    </script>
@endsection
