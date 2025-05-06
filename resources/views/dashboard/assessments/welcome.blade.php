@extends('layouts.main')

@section('title','Panel de administración')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }

    .info-modal {
        background-color: #ececec;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
    }

    .box {
        padding-top: 230px!important;
    }

    .modal-dialog {
        max-width: 700px!important;
        width: 100%!important;
    }

    .displayName {
        font-weight: bold;
        font-size: 17px;
    }

    .rawScore {
        font-size: 21px;
    }

    .view {
        display: block!important;
    }

    .btn-center {
        text-align: center;
        display: block;
        width: 170px;
        margin: 0 auto;
    }

    .octopuss {
        position: absolute;
        top: -100px;
        margin: 0 auto;
        width: 750px;
        text-align: center;
        display: block;
        left: 50%;
        transform: translate(-50%, 0);
    }

    span {
        font-style: italic;
        font-weight: 900;
        color: #033a60;
    }

    span.red {
        color: #f55936;
    }

    #dashboard .box h3 {
        margin-bottom: 15px;
        display:block;
    }

    .box-inner {
        padding: 60px!important;
    }

    #dashboard .box p {
        margin-bottom: 10px;
    }

    .btn-primary {
        background: #f55936;
        border: 0;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 16px!important;
    }

    .btn-primary:hover {
        background: #c93918;
    }

    .btn-secondary {
        background: #033a60;
        border: 0;
        padding: 12px 25px;
        font-weight: bold;
        border-radius: 12px;
        font-size: 16px!important;
    }

    .btn-secondary:hover {
        background: #024f85;
    }

    /*Móviles*/
    @media (max-width: 575px) {
        .box {
            padding-top: 100px !important;
        }

        .box-inner {
            padding: 30px !important;
        }

        .octopuss {
            width: 80%;
            top: -40px;
        }

        .btn {
            width:100%;
        }

        .btn-primary {
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@section('content')
<!-- Modal -->
<div class="modal fade" id="evaluationTypeModal" tabindex="-1" aria-labelledby="evaluationTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evaluationTypeModalLabel">Seleccionar tipo de evaluación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="evaluationTypeForm" method="POST" action="{{route('assessments.new')}}">
                    @csrf
                    <input type="hidden" name="respondentId" value="{{Auth::user()->account_id}}">
                    <input type="hidden" name="locale" value="{{Auth::user()->lang}}">
                    <div class="mb-3">
                        <label for="evaluationType" class="form-label">Tipo de evaluación</label>
                        <select class="form-select" id="evaluationType" name="type" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @if(in_array('short', Auth::user()->type_of_evaluation ?? []))
                                <option value="short">Evaluación corta (intereses) - 60 preguntas</option>
                            @endif
                            @if(in_array('long', Auth::user()->type_of_evaluation ?? []))
                                <option value="long">Evaluación larga (comportamientos, intereses y cognitivo) - 202 preguntas</option>
                            @endif
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="evaluationTypeForm">Continuar</button>
            </div>
        </div>
    </div>
</div>

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <img src="{{asset('assets/img/test.png')}}" alt="Octopus" class="img-fluid octopuss">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Hola <strong>{{Auth::user()->name}}</strong>, bienvenido a <span>Tu Talento</span> <span class="red">Finder</span></h3>
                                <div class="text-center">
                                    <p class="card-text">¿Qué deseas hacer hoy?</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationTypeModal">
                                        Asignar una evaluación
                                    </button>
                                    <a href="{{route('assessments.index',Auth::user()->account_id)}}" class="btn btn-secondary">Ver Historial</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="box-pink">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="box-inner">
                            <img src="{{asset('assets/img/logo-blue.png')}}" alt="{{env('APP_NAME')}}" class="text-center logo">
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
