@push('after_scripts')
    <script>
        $(document).ready(function() {
            $('#parent_data_table #family_type input:checked').trigger('change');
        });

        $('#parent_data_table').on('change', '#family_type input', function() {
            const type = $(this).val()

            let row = $(this).parent().parent()
            let parentesque_input = row.find(`#parentesque_person`);

            if (type === 'Encargado') {
                parentesque_input.removeClass('d-none')
            } else {
                parentesque_input.addClass('d-none')
            }
        })
    </script>
@endpush
