{{-- Show the inputs --}}
@foreach ($fields as $field)
    @php
      $fieldView = $crud->getFirstFieldView($field['type'], $field['view_namespace'] ?? false);
    @endphp

    @include($fieldView, ['field' => $field, 'inlineCreate' => true])
@endforeach

  <!--  This is where modal fields assets are pushed.
        We bind the modal content including what fields pushed to this stacks to the end of the body tag,
        so we make sure all backpack assets are previously loaded, like jquery etc.

        We can give the same stack name because backpack `crud_fields_scripts` is already rendered and
        this is the only available when rendering the modal html.
    -->
@stack('crud_fields_scripts')

@stack('crud_fields_styles')

