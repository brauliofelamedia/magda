@extends('layouts.main')

@section('title','Iniciar sesión')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }

    .items {
        margin:20px 0;
    }

    .item {
        padding: 30px;
        background-color: #ececec;
        border-radius: 10px;
        margin-bottom: 18px;
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
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12 text-center">
                                <h3 class="text-center" style="font-weight: bold;">Haz completado la evaluación con éxito.</h3>
                                <h4 class="text-center" style="margin-top:20px;">Resumen de tus intereses:</h4>
                                <div class="row">
                                    <div class="col-xl-8 offset-xl-2">
                                        <div class="items">
                                            <div class="row">
                                                @foreach($items as $item)
                                                    <div class="col-xl-4">
                                                        <div class="item">
                                                            <h3 class="text-center">{{$item['rawScore']}}%</h3>
                                                            <p class="text-center">{{$item['displayName']}}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="col-xl-12">
                                                    <a href="{{$reportPDF}}" target="_blank" download class="text-center btn btn-info" style="margin-top:20px;">Descargar en PDF el resumen</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!is_null($user->account_id))
                    <a href="{{route('assessments.index',$user->account_id)}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                @else
                    <a href="{{route('dashboard.welcome')}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                @endif
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