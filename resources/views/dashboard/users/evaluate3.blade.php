@extends('layouts.main')

@section('title','Evaluación')

@push('css')
@endpush

@section('content')

<div class="container" id="test3">
    <div class="row">
        <div class="col-xl-11 col-sm-9">
            <div class="row">
                <div class="col-xl-5">
                    <div class="vertical-align">
                        <h2 class="fw-700 c-orange">Todos tenemos<br/>un talento</h2>
                        <p>Indica del 1 al 5 qué tan<br/> de acuerdo te siente con<br/> las siguientes frases</p>
                    </div>
                </div>
                <div class="col-xl-3 offset-xl-4">
                    <img src="{{asset('assets/img/octopus-2.png')}}" class="f-right octopus b-block" alt="Octopus 2">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
                <div class="box-pink">
                    <div class="row mb-3">
                        <div class="col">
                            <h4 class="text-center">"Frase de ejemplo"</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="opt1" id="opt1">
                                <label class="form-check-label" for="opt1">1</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="opt1" id="opt2">
                                <label class="form-check-label" for="opt2">2</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="opt1" id="opt3">
                                <label class="form-check-label" for="opt3">3</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="opt1" id="opt4">
                                <label class="form-check-label" for="opt4">4</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="opt1" id="opt5">
                                <label class="form-check-label" for="opt5">5</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="{{route('evaluate.finish')}}" class="btn c-white bg-orange fw-600">Finalizar evaluación</a>
                            </div>
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