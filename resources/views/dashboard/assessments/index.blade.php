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

        .octopuss {
            width: 80%;
            top: -40px;
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
                        <input type="hidden" name="respondentId" value="{{$user->account_id}}">
                        <input type="hidden" name="locale" value="{{$user->lang}}">
                        <div class="mb-3">
                            <label for="evaluationType" class="form-label">Tipo de evaluación</label>
                            <select class="form-select" id="evaluationType" name="type" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                @if(in_array('short', $user->type_of_evaluation ?? []))
                                    <option value="short">Evaluación corta (intereses) - 60 preguntas</option>
                                @endif
                                @if(in_array('long', $user->type_of_evaluation ?? []))
                                    <option value="long">Evaluación larga (comportamientos, intereses y cognitivo) - 174 preguntas</option>
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
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $id = request()->route('respondentId');
        @endphp
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <img src="{{asset('assets/img/test.png')}}" alt="Octopus" class="img-fluid octopuss">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                @role('respondent')
                                    <h3 class="text-center">Mis evaluaciones</h3>
                                @endrole
                                @hasanyrole(['administrator','institution'])
                                    <h3 class="text-center">Evaluaciones de <strong>{{$user->name}}</strong></h3>
                                @endhasanyrole
                                <hr>
                                @if(($assesments))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="hidden-media">#</th>
                                                <th scope="col" class="hidden-media">Iniciada</th>
                                                <th scope="col">Estatus</th>
                                                <th scope="col">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assesments as $assesment)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($assesment['node']['startedOn']);
                                                        $submit = \Carbon\Carbon::parse($assesment['node']['submittedOn']);
                                                    @endphp
                                                    <tr>
                                                        <th class="hidden-media" scope="row">{{$assesment['node']['id']}}</th>
                                                        <td class="hidden-media">{{$start->format('d-m-Y')}}</td>
                                                        <td><strong>{{$assesment['node']['status']}}</strong></td>
                                                        <td>
                                                            @if(!$assesment['node']['status'] == 'EXPIRED' OR !$assesment['node']['status'] == 'SUBMITTED')
                                                                <a class="btn btn-primary click-send-email" data-assesment="{{$assesment['node']['id']}}" @if($assesment['node']['status'] != 'EXPIRED') @else disabled @endif>Solicitar evaluación</a>
                                                            @elseif($assesment['node']['status'] == 'STARTED')
                                                                <a class="btn btn-continue" href="{{route('assessments.continue',[$id,$assesment['node']['id'],$assesment['node']['token'],$assesment['node']['locale']])}}">Continuar</a>
                                                            @elseif($assesment['node']['status'] == 'EXPIRED')
                                                                <a class="btn btn-danger btn-disabled btn-caducade btn-xs" href="#" disabled>Caducado</a>
                                                            @endif
                                                            @if($assesment['node']['status'] == 'FINISHED' OR $assesment['node']['status'] == 'SUBMITTED')
                                                                <a class="btn btn-success" href="{{route('assessments.finish',$assesment['node']['id'])}}">Resultados</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center">El usuario no tiene evaluaciones</p>
                                @endif
                                    <div class="text-center">
                                        @hasrole('respondent')
                                            <!-- Button to trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationTypeModal">
                                                Iniciar nueva evaluación
                                            </button>
                                            <a href="{{route('dashboard.welcome')}}" class="btn btn-secondary">Regresar</a>
                                        @elseif('institution')
                                            <!-- Button to trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationTypeModal">
                                                Asignar una evaluación
                                            </button>
                                            <a href="{{route('dashboard.welcome')}}" class="btn btn-secondary">Regresar</a>
                                        @else
                                            <a href="{{route('dashboard.welcome')}}" class="btn btn-secondary">Regresar</a>
                                        @endhasrole
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
<script>
    $(document).ready(function(){
        $('.btn-clear').on('click', function(){
            $('.data-container').empty();
            $('.wait').css('display','block');
        });

        //Ver resultados de la evaluación
        $('.click-assesment').on('click', function(){
            $('.data-container').empty();
            $('.download-pdf').removeClass('view');

            let id = $(this).data('report');
            let locale = $(this).data('locale');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Preparar los datos de la solicitud
            let data = {
                id: id,
                locale: locale,
                _token: csrfToken
            };

            $.ajax({
                url: "{{route('report.results')}}",
                method: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $.each(response.data, function(key, value) {

                            const element = $(`<div class="col-md-4">`).html(`<div class="info-modal"><h5 class="text-center displayName">${value.displayName}</h5><p class="rawScore text-center">${value.rawScore}</p></div>`);
                            $('.data-container').append(element);
                            $('.wait').css('display','none');

                        });

                        //Rellenar boton de descarga
                        $('.download-pdf').attr("href", response.data2);
                        $('.download-pdf').addClass('view');
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error al generar el reporte. Inténtalo de nuevo más tarde.");
                }
            });
        });

        //Solicitar link de evaluación
        $('.click-send-email').on('click', function(){

            let id = $(this).data('assesment');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Preparar los datos de la solicitud
            let data = {
                id: id,
                _token: csrfToken
            };

            $.ajax({
                url: "{{route('users.email.welcome')}}",
                method: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        alert(response.success);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                    alert("Error al generar el reporte. Inténtalo de nuevo más tarde.");
                }
            });
            });
    });
</script>
@endpush
