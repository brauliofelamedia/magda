@extends('layouts.main')

@section('title','Iniciar sesión')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }
    .box .box-inner {
        margin-top: 210px!important;
    }
    .alga {
        top: 0 !important;
        bottom: 0 !important;
    }
    .coral {
        top: 0 !important;
        bottom: 0 !important;
    }
</style>
</style>
@endpush

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                @include('parts.message')
                <div class="box">
                    <img src="{{asset('assets/img/octopus.png')}}" alt="" class="octopus">
                    <img src="{{asset('assets/img/alga.png')}}" alt="" class="alga">
                    <img src="{{asset('assets/img/burbujas.png')}}" alt="" class="burbujas">
                    <img src="{{asset('assets/img/coral.png')}}" alt="" class="coral">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Usuarios</h3>
                                {{ $dataTable->table() }}
                                @role('administrator')
                                    <a href="{{route('dashboard.sync')}}" class="center-h btn btn-danger">Sincronizar usuarios</a>
                                @endrole
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
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function(){
        $('body').on('click','.btn-send-welcome', function(event){
            event.preventDefault();

            Swal.fire({
                title: "Esta un momento",
                text: "Estamos enviando el correo",
                icon: "info"
            });

            let id = $(this).data('id');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Preparar los datos de la solicitud
            let data = {
                id: id,
                _token: csrfToken
            };

            $.ajax({
                url: "{{ route('users.email.welcome') }}",
                method: "POST", 
                data: data,
                success: function(response) {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Se ha enviado el correo",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endpush