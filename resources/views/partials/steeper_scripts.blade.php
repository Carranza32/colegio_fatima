@section('after_styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">
@endsection

<div class="bs-stepper">
    <div class="bs-stepper-header" role="tablist">
        <!-- your steps here -->
        <div class="step" data-target="#alumno">
            <button type="button" class="step-trigger" role="tab" aria-controls="alumno" id="alumno-trigger">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Alumno</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#padres">
            <button type="button" class="step-trigger" role="tab" aria-controls="padres" id="padres-trigger">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Padres</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#ocupacion-part">
            <button type="button" class="step-trigger" role="tab" aria-controls="ocupacion-part" id="ocupacion-part-trigger">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Ocupacion</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#documentos-part">
            <button type="button" class="step-trigger" role="tab" aria-controls="documentos-part" id="documentos-part-trigger">
                <span class="bs-stepper-circle">3</span>
                <span class="bs-stepper-label">Documentos</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#observaciones-part">
            <button type="button" class="step-trigger" role="tab" aria-controls="observaciones-part" id="observaciones-part-trigger">
                <span class="bs-stepper-circle">4</span>
                <span class="bs-stepper-label">Observaciones</span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#sigies-part">
            <button type="button" class="step-trigger" role="tab" aria-controls="sigies-part" id="sigies-part-trigger">
                <span class="bs-stepper-circle">5</span>
                <span class="bs-stepper-label">SIGIES</span>
            </button>
        </div>
    </div>
    <div class="bs-stepper-content">
        <!-- your steps content here -->
        <div id="alumno-part" class="content" role="tabpanel" aria-labelledby="alumno-part-trigger">
            <h3>alumno</h3>
        </div>
        <div id="padres-part" class="content" role="tabpanel" aria-labelledby="padres-part-trigger">
            <h3>padres</h3>
        </div>
        <div id="ocupacion-part" class="content" role="tabpanel" aria-labelledby="ocupacion-part-trigger">
            <h3>ocupacion</h3>
        </div>
        <div id="documentos-part" class="content" role="tabpanel" aria-labelledby="documentos-part-trigger">
            <h3>documentos</h3>
        </div>
        <div id="observaciones-part" class="content" role="tabpanel" aria-labelledby="observaciones-part-trigger">
            <h3>observaciones</h3>
        </div>
        <div id="sigies-part" class="content" role="tabpanel" aria-labelledby="sigies-part-trigger">
            <h3>sigies</h3>
        </div>
    </div>
</div>

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var stepperSelector = document.querySelector('.bs-stepper');
            var stepper = new Stepper(stepperSelector);
            stepper.to(2);
        });
    </script>
@endsection
