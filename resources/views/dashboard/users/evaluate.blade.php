@extends('layouts.main')

@section('title','Evaluación')

@push('css')
@endpush

@section('content')

<div class="container" id="test1">
    <div class="row">
        <div class="col-xl-11 col-sm-9">
            <div class="row">
                <div class="col-xl-4">
                    <div class="vertical-align">
                        <h2 class="fw-700 c-orange">Selecciona <br/>los pares</h2>
                        <p>Selecciona las siguientes<br/> opciones según tus<br/> preferencias.</p>
                    </div>
                </div>
                <div class="col-xl-3 offset-xl-5">
                    <img src="{{asset('assets/img/octopus-1.png')}}" class="f-right octopus b-block" alt="Octopus">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="box-pink">
                <div class="row">

                    <div class="col-xl-6">
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
                    <div class="col-xl-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt5">
                            <label class="form-check-label" for="opt5">Opción 5</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt6">
                            <label class="form-check-label" for="opt6">Opción 6</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt7">
                            <label class="form-check-label" for="opt7">Opción 7</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opt1" id="opt8">
                            <label class="form-check-label" for="opt8">Opción 8</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <a href="{{route('users.evaluate',2)}}" class="btn c-white bg-orange fw-600">Confirmar mi respuesta</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush