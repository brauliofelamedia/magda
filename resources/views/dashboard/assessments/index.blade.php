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
</style>
@endpush

@section('content')
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
                                                            <a class="btn btn-primary" href="{{route('assessments.continue',[$id,$assesment['node']['id'],$assesment['node']['token'],$assesment['node']['locale']])}}">Continuar</a>
                                                        @elseif($assesment['node']['status'] == 'EXPIRED')
                                                            <a class="btn btn-danger btn-disabled btn-xs" href="#" disabled>Caducado</a>
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
                                    <a href="{{route('assessments.new',[$user->account_id,$user->lang])}}" class="btn btn-success btn-xl">Crear evaluación</a>
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
                                <a href="#">
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