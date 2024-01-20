@include('crud::fields.inc.wrapper_start')
<div class="form-group {{ $field['wrapper']['class'] }}">
    <label>{!! $field['label'] !!}</label>

    <div class="d-flex">
        <div>
            <canvas id="{{ $field['name'] }}Canvas" width="400" height="200" class="border"></canvas>
            <button type="button" id="clearSignature" class="btn btn-warning d-block">Limpiar Firma</button>

            <input type="hidden" name="{{ $field['name'] }}" id="signatureInput" value="">
        </div>

        <div class="box-body">
            @isset ($field['value'])
                <img src="{{ $field['value'] }}" alt="Firma">
            @endisset
        </div>
    </div>
    @include('crud::fields.inc.translatable_icon')
</div>
@include('crud::fields.inc.wrapper_end')

@section('after_scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var canvas = document.getElementById('{{ $field['name'] }}Canvas');
        var signaturePad = new SignaturePad(canvas);

        // Al cambiar la firma, actualiza el campo oculto con los datos de la firma en formato base64
        signaturePad.addEventListener("endStroke", () => {
            document.getElementById('signatureInput').value = signaturePad.toDataURL();
        });

        // Manejador de evento para el botón "Limpiar Firma"
        document.getElementById('clearSignature').addEventListener('click', function () {
            signaturePad.clear();
            document.getElementById('signatureInput').value = '';
        });
    });
</script>
@endsection
