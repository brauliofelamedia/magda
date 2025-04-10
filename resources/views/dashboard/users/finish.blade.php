@extends('layouts.main')

@section('title','Resultados de la evaluación')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }

    .items {
        margin:20px 0;
    }

    .box {
        padding-top: 230px!important;
    }

    .vertical-align p {
        margin: 0!important;
    }

    .octopuss {
        width: 60%;
        margin: 0 auto;
        display: block;
        position: absolute;
        top: -70px;
        left: 50%;
        transform: translate(-50%, 0);
    }

    ol,ul {
        text-align: left!important;
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

    /*Móviles*/
    @media (max-width: 575px) {
        .box {
            padding-top: 100px !important;
        }

        .octopuss {
            width: 80%;
            top: -10px;
        }
    }

    /*Tablets*/
    @media (min-width: 768px) {
    }

    /*Laptops*/
    @media (min-width: 992px) {
    }

    /*Desktop*/
    @media (min-width: 1200px) {
    }

    .resume {
        background-color: #ececec;
        padding: 30px;
        border-radius: 10px;
        margin-top: 30px;
    }

    .resume h2 {
        font-weight: bold;
        color: #f7423e;
        font-size: 26px;
        margin-bottom: 20px;
    }

    .resume h4 {
        font-size: 18px;
        font-weight: 600;
    }

    .resume h1 {
        font-size: 32px;
        font-weight: bold;
        line-height: 1.1em;
        margin-bottom: 21px;
    }

    .resume h3 {
        font-size: 19px;
        margin-bottom: 10px;
    }

    .prompt-text p, .prompt-text li, .prompt-text ol, .prompt-text strong {
        font-size: 14px !important;
        margin-bottom: 0;
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
                    <img src="{{asset('assets/img/finish.png')}}" alt="" class="octopuss">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12 text-center">
                                <h3 class="text-center" style="font-weight: bold;color:#F74219;">Haz completado la evaluación con éxito.</h3>
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
                                                    <h4>Descargar:</h4>
                                                    @if(isset($pdf_individual))
                                                        <a href="{{$pdf_individual}}" target="_blank" download class="text-center btn btn-info" style="margin-top:20px;color:white;background: #0DC863!important;">Informe Individual</a><br>
                                                    @endif
                                                    @if(isset($pdf_interest))
                                                        <a href="{{$pdf_interest}}" target="_blank" download class="text-center btn btn-info" style="margin-top:20px;color:white;background: #0DC863!important;">Informe de Intereses de Orientación Profesional</a><br>
                                                    @endif
                                                    @if(isset($assessment->openia))
                                                        <div class="resume">
                                                            {!!$assessment->openia!!}
                                                        </div>
                                                    @endif
                                                    @if(!is_null($user->account_id))
                                                        <a href="{{route('assessments.index',$user->account_id)}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                                                    @else
                                                        <a href="{{route('dashboard.welcome')}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                                                    @endif
                                                </div>
                                                <div class="prompt">
                                                    <div class="col-xl-12">
                                                        <div class="resume" style="margin-top: 30px;background-color: #f9f9f9;">
                                                            <h4>Prompt utilizado para el análisis:</h4>
                                                            <div class="prompt-text" style="background-color: #fff!important; padding: 15px; border-radius: 5px; margin: 10px 0; height: 180px; overflow-y: auto;">
                                                                <p>Actúa como un orientador vocacional con experiencia en desarrollo de carrera y análisis de perfiles. A continuación, recibirás un informe completo de intereses ocupacionales generado a través del assessment 'Tu Talento Finder' para un individuo. Tu tarea es leer y analizar dicho informe con atención.</p>
                                                                <p>Basándote en:</p>
                                                                <ul>
                                                                    <li>Los tres intereses ocupacionales más altos del participante (en orden de prioridad).</li>
                                                                    <li>Las descripciones detalladas de esos tipos de interés.</li>
                                                                    <li>Las ocupaciones sugeridas en las categorías profesionales del informe.</li>
                                                                    <li>La compatibilidad porcentual si está incluida.</li>
                                                                    <li>Los pasatiempos y motivadores asociados a los intereses dominantes.</li>
                                                                </ul>
                                                                <p>Genera lo siguiente:</p>
                                                                <ol>
                                                                    <li>Las <strong>5 profesiones ideales</strong> para el participante, al día de hoy, que estén alineadas con sus intereses, motivadores y nivel de preparación actual (puedes hacer suposiciones razonables si no se incluye nivel de estudios).</li>
                                                                    <li>Las <strong>5 mejores ideas de emprendimiento</strong> que podrían entusiasmar y retar al participante, considerando sus motivadores personales, como el liderazgo, la autonomía, la creatividad o la interacción con personas.</li>
                                                                    <li>Justifica brevemente cada recomendación (1-2 líneas por cada profesión o emprendimiento).</li>
                                                                </ol>
                                                                <p class="mt-3"><strong>IMPORTANTE:</strong> Las recomendaciones deben ser prácticas, relevantes al contexto actual del mercado laboral, y ofrecer tanto opciones tradicionales como innovadoras. Sé concreto, creativo y profesional.</p>
                                                            </div>
                                                            <button class="btn btn-secondary copy-prompt" style="margin-top: 10px;">
                                                                <i class="fas fa-copy"></i> Copiar Prompt
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.copy-prompt').click(function() {
            var promptText = $('.prompt-text').find('p, li, strong').map(function() {
                return $(this).text();
            }).get().join('\n');
            navigator.clipboard.writeText(promptText).then(function() {
                alert('Prompt copiado al portapapeles');
            }).catch(function() {
                alert('Error al copiar el prompt');
            });
        });
    });
</script>
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