@extends('layouts.main')

@section('title','Iniciar sesión')

@push('css')
<style>
    .example-file {
        color: #f74219;
        text-decoration: underline;
        text-align: center;
        display: block;
    }

    .box-inner {
        margin-top: 220px !important;
    }

    .btn-submit {
        padding: 15px 25px !important;
        font-size: 16px !important;
        font-weight: bold !important;
        background-color: #033a60 !important;
        border: 0 !important;
    }

    .btn-submit:hover {
        background-color: #035f9f !important;
    }
</style>
@endpush

@section('content')

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <img src="{{asset('assets/img/octopus.png')}}" alt="" class="octopus">
                    <img src="{{asset('assets/img/alga.png')}}" alt="" class="alga">
                    <img src="{{asset('assets/img/burbujas.png')}}" alt="" class="burbujas">
                    <img src="{{asset('assets/img/coral.png')}}" alt="" class="coral">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">

                                <h2 class="text-center">Importación de usuarios</h2>

                                <form action="{{route('dashboard.process.import')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="file">Adjunta el archivo de excel:</label>
                                        <input type="file" class="form-control" name="file" id="file" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-submit"><i class="fa-solid fa-arrows-rotate"></i> Importar usuarios</button>
                                </form>

                                <br><br>
                                <a href="#" class="example-file">Revisa el ejemplo para importar usuarios.</a>
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
