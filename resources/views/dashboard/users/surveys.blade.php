@extends('layouts.main')

@section('title','Encuestas')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }
</style>
@endpush

@section('content')

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Encuestas de <strong>{{$user->name}}</strong></h3>
                                <hr>
                                <table class="table">
                                    <thead>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Idioma</th>
                                        <th scope="col">Enviada</th>
                                        <th scope="col">Terminada</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col" width="240">Acciones</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assesments as $assesment)

                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                Launch demo modal
                                            </button>
                                            
                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="false" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-nobackdrop">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                    ...
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
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
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <button type="button" class="btn @if($submit) disabled @else btn-primary @endif" @if($submit) disabled @endif>Evaluar</button>
                                                        <button type="button" class="btn btn-success">Ver resultados</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                  </table>
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
                        <div class="col-xl-6">
                            <div class="box-inner text-center">
                                <img src="{{asset('assets/img/doc.png')}}" class="mb-3">
                                <a href="{{route('test.results')}}">
                                    <h4>Ver resultados</h4>
                                </a>
                                <p>Conoce cómo te fue</p>
                            </div>
                        </div>
                        <div class="col-xl-6">
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
@endpush