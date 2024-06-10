@extends('layouts.main')

@section('title','Iniciar sesión')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }
</style>
@endpush

@section('content')

    
    @foreach($assesments as $assesment)
        <div class="modal fade" id="{{$assesment['node']['id']}}-Modal" tabindex="-1" aria-labelledby="{{$assesment['node']['id']}}-ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-nobackdrop">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="{{$assesment['node']['id']}}-ModalLabel">Resultados</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
            </div>
        </div>
    @endforeach

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Evaluaciones de <strong>{{$user->name}}</strong></h3>
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
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$assesment['node']['id']}}-Modal">Resultados</button>
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
    </div>
@endsection

@push('js')
@endpush