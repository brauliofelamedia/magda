@extends('layouts.main')

@section('title','Evaluación')

@push('css')
@endpush

@section('content')

<div class="container" id="test2">
    <div class="row">
        <div class="col-xl-11 col-sm-9">
            <div class="row">
                <div class="col-xl-4">
                    <div class="vertical-align">
                        <h2 class="fw-700 c-orange">Esta situación <br/>te resulta...</h2>
                        <p>Imagina que estás atravesando la siguiente situación y selecciona la que más se acerque a cómo te sentirías.</p>
                    </div>
                </div>
                <div class="col-xl-3 offset-xl-5">
                    <img src="assets/img/octopus-2.png" class="f-right octopus b-block" alt="Octopus 2">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="box-pink">
                <div class="row mb-3">
                    <div class="col">
                        <h4 class="text-center">"Muestra de una situación<br/> de ejemplo"</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt1">
                            <label class="form-check-label" for="opt1">Opción 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt2">
                            <label class="form-check-label" for="opt2">Opción 2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt3">
                            <label class="form-check-label" for="opt3">Opción 3</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt4">
                            <label class="form-check-label" for="opt4">Opción 4</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <a href="{{route('users.evaluate',3)}}" class="btn c-white bg-orange fw-600">Confirmar mi respuesta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
@endpush