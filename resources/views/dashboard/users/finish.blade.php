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

    h3 {
        text-align: left;
    }

    p {
        font-size: 16px!important;
        text-align: left!important;
        line-height: 1.2em!important;
        margin-bottom: 20px!important;
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
                                <div class="row">
                                    <div class="col-xl-8 offset-xl-2">
                                        <p class="text-center">El siguiente gráfico muestra su perfil de intereses y, con el fin de explorar su carrera profesional, le recomendamos que se concentre en las 3 puntuaciones más altas, comenzando con la primera. Sin embargo, tenga en cuenta que lo más relevante no es la intensidad (grado) del interés, sino el orden de prioridad lo que puede afectar a la compatibilidad con una ocupación en particular. Las seis escalas le proporcionan información acerca de lo que le motiva o el tipo de trabajo que le atrae.</p>
                                        <div class="items">
                                            <div class="row">
                                                @foreach($items as $item)
                                                    <div class="col-xl-4">
                                                        <div class="item">
                                                            <h3 class="text-center">{{$item['rawScore']}}%</h3>
                                                            <span class="text-center">{{$item['displayName']}}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="col-xl-12">
                                                    <canvas id="myChart"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const items = @json($items);
    console.log(items[0]);
    const myChart = new Chart(ctx, {
        type: 'bar',
        data:   
        {
            labels: [items[0]['displayName'], items[1]['displayName'], items[2]['displayName'], items[3]['displayName'], items[4]['displayName'], items[5]['displayName']],
            datasets: [{
                label: 'Porcentaje de interés',
                data: [items[0]['rawScore'], items[1]['rawScore'], items[2]['rawScore'], items[3]['rawScore'], items[4]['rawScore'], items[5]['rawScore']],
                backgroundColor: [
                    '#fda327',
                    '#68c133',
                    '#00bdd1',
                    '#a54ea4',
                    '#e4cc00',
                    '#f44743'
                ],
                borderColor: [
                    '#fda327',
                    '#68c133',
                    '#00bdd1',
                    '#a54ea4',
                    '#e4cc00',
                    '#f44743'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
  </script>
@endpush