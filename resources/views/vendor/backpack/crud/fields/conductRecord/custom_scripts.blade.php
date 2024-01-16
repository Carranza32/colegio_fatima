@push('after_scripts')
    <script>
        $(document).ready(function() {
            $('#action input:checked').trigger('change');
            $("#course_id").trigger('change');
        });

        $('#action').on('change', function() {
            const type = $('#action input:checked').val();
            const course = $("#course_id");
            const student = $("#student_id");
            const teacher = $("#teacher_id");

            if (type === 'Estudiantes') {
                course.removeClass('d-none')
                student.removeClass('d-none')
                teacher.addClass('d-none')
            } else {
                course.addClass('d-none')
                student.addClass('d-none')
                teacher.removeClass('d-none')
            }
        })

        $("#course_id").on('change', function() {
            let value = $("#course_id option:selected").val();

            $.ajax({
                url: "{{ route('alumns.by_course') }}",
                method: 'POST',
                data: {
                    curso_id: value
                },
                success: function (response) {
                    $('#student_id select').empty()

                    if (response) {
                        response?.forEach(el => {
                            $('#student_id select').append(`
                                <option value="${el?.id}">${el?.full_name}</option>
                            `)
                        })
                    }
                }
            })
        })
    </script>
@endpush
