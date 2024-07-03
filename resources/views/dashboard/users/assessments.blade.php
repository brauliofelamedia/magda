@extends('layouts.main')

@section('title','Iniciar sesión')

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
</style>
@endpush

@section('content')
    @if($assesments)
        @foreach($assesments as $assesment)
            <div class="modal fade" id="{{$assesment['node']['id']}}-Modal" tabindex="-1" aria-labelledby="{{$assesment['node']['id']}}-ModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-nobackdrop">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="{{$assesment['node']['id']}}-ModalLabel">Resultados de intereses</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="wait"><p class="text-center">Espera un momento...</p></div>
                        <div id="data-container" class="row">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-clear" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="container" id="dashboard">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                @hasanyrole(['administrator','institution'])
                                    <a href="{{route('dashboard.welcome')}}" class="btn btn-primary">Volver</a>
                                @endhasanyrole
                                @role('respondent')
                                    <h3 class="text-center">Mis evaluaciones</h3>
                                @endrole
                                @hasanyrole(['administrator','institution'])
                                    <h3 class="text-center">Evaluaciones de <strong>{{$user->name}}</strong></h3>
                                @endhasanyrole
                                <hr>
                                @if(($assesments))
                                <table class="table">
                                    <thead>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Idioma</th>
                                        <th scope="col">Enviada</th>
                                        <th scope="col">Terminada</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col" width="350">Acciones</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assesments as $assesment)
                                            @php
                                                $start = \Carbon\Carbon::parse($assesment['node']['startedOn']);
                                                $submit = \Carbon\Carbon::parse($assesment['node']['submittedOn']);
                                            @endphp
                                            <tr>
                                                <th scope="row">{{$assesment['node']['id']}}</th>
                                                <td>{{$assesment['node']['locale']}}</td>
                                                <td>{{$submit->format('d-m-Y')}}</td>
                                                <td>{{$start->format('d-m-Y')}}</td>
                                                <td><strong>{{$assesment['node']['status']}}</strong></td>
                                                <td>
                                                    @if(!$assesment['node']['status'] == 'EXPIRED' OR !$assesment['node']['status'] == 'SUBMITTED')
                                                        <a class="btn btn-primary click-send-email" data-assesment="{{$assesment['node']['id']}}" @if($assesment['node']['status'] != 'EXPIRED') @else disabled @endif>Solicitar evaluación</a>
                                                    @elseif($assesment['node']['status'] == 'SUBMITTED')
                                                        <a href="#" class="btn btn-info disabled" disabled>Contestado</a>
                                                    @elseif($assesment['node']['status'] == 'NEW')
                                                        <a class="btn btn-success" href="{{route('evaluate.start',[$assesment['node']['id'],$assesment['node']['token'],$assesment['node']['locale']])}}">Evaluar</a>
                                                    @elseif($assesment['node']['status'] == 'STARTED')
                                                        <a class="btn btn-success" href="{{route('users.evaluate',[$assesment['node']['id'],$assesment['node']['token'],$assesment['node']['locale']])}}">Continuar</a>
                                                    @else
                                                        <a href="#" class="btn btn-danger disabled" disabled>Expirado</a>
                                                    @endif
                                                    @if($assesment['node']['status'] == 'FINISHED' OR $assesment['node']['status'] == 'SUBMITTED')
                                                        <a class="btn btn-info click-assesment" data-report="{{$assesment['node']['id']}}" data-bs-toggle="modal" data-bs-target="#{{$assesment['node']['id']}}-Modal">Resultados</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                 <p class="text-center">El usuario no tiene evaluaciones</p>
                                @endif
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
                            <div class="box-inner text-center">
                                <img src="{{asset('assets/img/doc.png')}}" class="mb-3">
                                <a href="{{route('users.edit',auth()->user()->uuid)}}">
                                    <h4>Perfil</h4>
                                </a>
                                <p>Editar información</p>
                            </div>
                        </div>
                        <div class="col-xl-6" style="display: none;">
                            <div class="box-inner text-center">
                                <img src="{{asset('assets/img/configuration.png')}}" class="mb-3">
                                <a href="{{route('test.settings')}}">
                                    <h4>Ajustes</h4>
                                </a>
                                <p>Configuración</p>
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
            $('#data-container').empty();
            $('.wait').css('display','block');
        });

        //Ver resultados de la evaluación
        $('.click-assesment').on('click', function(){

            let id = $(this).data('report');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Preparar los datos de la solicitud
            let data = {
                id: id,
                _token: csrfToken
            };

            $.ajax({
                url: "{{route('users.report')}}",
                method: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $.each(response.data, function(key, value) {
                            const element = $(`<div class="col-md-4">`).html(`<div class="info-modal"><h5 class="text-center displayName">${value.displayName}</h5><p class="rawScore text-center">${value.rawScore}</p></div>`);
                            $('#data-container').append(element);
                            $('.wait').css('display','none');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
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
                url: "{{route('users.sendEmail')}}",
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