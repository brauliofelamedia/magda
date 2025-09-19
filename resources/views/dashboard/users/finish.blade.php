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

    .img-ia {
        width: 380px;
        display: block;
        margin: 20px auto;
        margin-bottom: 0;
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

    .prompt {
        margin-top: 30px;.
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding:30px;
        border: 1px solid #ececec;
        border-radius: 10px;
    }

    .prompt-text {
        text-align: left;
        background-color: #fff;
        padding: 20px; 
        border-radius: 8px; 
        margin: 10px 0; 
        height: 200px; 
        overflow-y: auto; 
        border: 1px solid #e0e0e0;
    }

    .prompt-text h5 {
        margin: 10px 0;
        font-size: 18px;
    }

    .prompt-text p, .prompt-text li, .prompt-text ol, .prompt-text strong {
        font-size: 14px !important;
        margin-bottom: 0;
        text-align: left;
    }

    .btn-success {
        margin:0 auto;
        margin: 0 auto;
        margin-bottom: 10px;
        padding: 12px 25px;
        font-weight: 500;
    }

    @media (max-width: 480px) {
        .prompt {
            padding:25px;
        }

        .prompt li {
            font-size: 14px;
            line-height: 17px;
        }

        .prompt-text {
            padding: 15px;
        }

        .prompt-text ol {
            padding-left: 15px;
        }
        
        .prompt-text strong {
            color: #e74c3c;
            display: block;
            line-height: 14px;
            margin: 15px 0;
        }

        .resume h1 {
            font-size: 20px;
        }

        .resume {
            padding: 20px;
        }

        .resume h2 {
            font-size: 20px;
            margin-bottom: 19px;
        }
    }

    @media (max-width: 768px) {
        
    }

    @media (max-width: 992px) {
        
    }
    
    .interpreter-collapse .interpreter-header {
        padding: 10px 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .interpreter-collapse .interpreter-header:hover {
        background-color: #e9ecef;
    }
    
    .interpreter-collapse .interpreter-body {
        padding: 0 15px;
    }
    
    .interpreter-header .fa-chevron-down {
        transition: transform 0.3s ease;
        margin-left: 10px;
    }
    
    .interpreter-header[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
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

                                                    @if(isset($assessment->openia) && !empty($assessment->openia))
                                                        <div class="resume" style="margin-top: 30px;box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                            <h4 style="margin-bottom:20px;padding:13px 20px; border-radius:20px;background-color:white;"><img src="{{asset('assets/img/chatgpt.png')}}" alt="ChatGPT" style="width: 120px; height: 40px; margin-right: 10px;"> Interpretación usando ChatGPT</h4>
                                                            {!!$assessment->openia!!}
                                                        </div>
                                                    @elseif($assessment->is_processing == 1)
                                                        <div class="resume" style="margin-top: 30px;box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                            <div class="text-center">
                                                                <div class="spinner-border text-primary" role="status">
                                                                    <span class="visually-hidden">Cargando...</span>
                                                                </div>
                                                                <h3>Estamos generando tu análisis personalizado</h3>
                                                                <p>Por favor espera unos momentos o recarga la página en unos instantes...</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if(isset($assessment->resumen_openia) && empty($assessment->resumen_openia))
                                                        <div class="resume interpreter-collapse" style="margin-top: 30px;box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                            <div class="interpreter-header" data-toggle="collapse" data-target="#interpreterContent" aria-expanded="false" aria-controls="interpreterContent" style="cursor: pointer;">
                                                                <div style="display: flex; align-items: center;">
                                                                    <img src="{{asset('assets/img/chatgpt.png')}}" alt="ChatGPT" style="width: 120px; height: 40px; margin-right: 10px;">
                                                                    <h4 style="margin: 0;">Ver interpretación</h4>
                                                                    <i class="fas fa-chevron-down ml-2"></i>
                                                                </div>
                                                            </div>
                                                            <div class="collapse" id="interpreterContent">
                                                                <div class="interpreter-body" style="padding-top: 15px;">
                                                                    {!!$assessment->resumen_openia!!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="prompt">
                                                        <div class="col-xl-12">
                                                            <div>
                                                                <div class="usage-tips" style="margin-bottom: 20px;">
                                                                    <h4 style="color: #333;">💡 ¿Cómo puedes usarlo?</h4>
                                                                    <ul style="list-style: none; padding-left: 0;">
                                                                        <li style="margin-bottom: 10px;">
                                                                            <i class="fas fa-check" style="color: #68c133; margin-right: 8px;"></i>
                                                                            Puedes copiar este prompt y pegarlo directamente en Claude, Gemini u otra IA.
                                                                        </li>
                                                                        <li style="margin-bottom: 10px;">
                                                                            <i class="fas fa-check" style="color: #68c133; margin-right: 8px;"></i>
                                                                            Puedes usarlo en tu propia plataforma si integras GPT con Tu Talento Finder.
                                                                        </li>
                                                                        <li style="margin-bottom: 10px;">
                                                                            <i class="fas fa-check" style="color: #68c133; margin-right: 8px;"></i>
                                                                            Puedes incluso automatizarlo si haces una integración por lotes para generar informes de orientación.
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="prompt-text">
                                                                <p style="color: #2c3e50;font-weight: 600;font-size: 18px !important;">Basándote en:</p>
                                                                <ol>
                                                                    <li>Los tres intereses ocupacionales más altos del participante (en orden de prioridad).</li>
                                                                    <li>Las descripciones detalladas de esos tipos de interés.</li>
                                                                    <li>Las ocupaciones sugeridas en las categorías profesionales del informe.</li>
                                                                    <li>La compatibilidad porcentual si está incluida.</li>
                                                                    <li>Los pasatiempos y motivadores asociados a los intereses dominantes.</li>
                                                                </ol>
                                                                <p style="color: #2c3e50;font-weight: 600;font-size: 18px !important;">Genera lo siguiente:</p>
                                                                <ol>
                                                                    <li>Las **5 profesiones ideales** para el participante, al día de hoy, que estén alineadas con sus intereses, motivadores y nivel de preparación actual.</li>
                                                                    <li>Las **5 mejores ideas de emprendimiento** que podrían entusiasmar y retar al participante.</li>
                                                                    <li>Justifica brevemente cada recomendación (1-2 líneas por cada profesión o emprendimiento).</li>
                                                                </ol>
                                                                <strong style="color: #e74c3c;">IMPORTANTE: Las recomendaciones deben ser prácticas y relevantes al contexto actual del mercado laboral.</strong>
                                                                <p>Este es el informe para analizar:</p>
                                                                <p>[pega el contenido del informe aquí o súbelo como archivo PDF]</p>
                                                                </div>
                                                                <button class="btn btn-secondary copy-prompt" style="margin-top: 15px;padding: 8px 20px;">
                                                                    <i class="fas fa-copy"></i> Copiar Prompt
                                                                </button>
                                                                <img src="{{asset('assets/img/logo-ia.png')}}" alt="OpenAI" class="img-fluid img-ia">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="download-section" style="margin-top: 30px;">
                                                        <h4 style="color: #333;margin-bottom: 15px;">Descargar informes:</h4>
                                                        <div class="d-flex flex-column gap-2">
                                                            @if(isset($pdf_individual))
                                                                <a href="{{$pdf_individual}}" target="_blank" download class="btn btn-success">
                                                                    <i class="fas fa-file-pdf mr-2"></i> Individual
                                                                </a>
                                                            @endif
                                                            @if(isset($pdf_interest))
                                                                <a href="{{$pdf_interest}}" target="_blank" download class="btn btn-success">
                                                                    <i class="fas fa-file-pdf mr-2"></i> Intereses de orientación profesional
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                   
                                                    @if(!is_null($user->account_id))
                                                        <a href="{{route('assessments.index',$user->account_id)}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                                                    @else
                                                        <a href="{{route('dashboard.welcome')}}" class="btn btn-success" style="margin-top:20px;display: inline-block;">Regresar</a>
                                                    @endif
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
        
        $('.interpreter-header').click(function() {
            var expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
            $(this).find('.fa-chevron-down').css('transform', !expanded ? 'rotate(180deg)' : 'rotate(0)');
            // Activar el collapse de Bootstrap usando el método nativo de Bootstrap 5
            var interpreterCollapse = bootstrap.Collapse.getOrCreateInstance(document.getElementById('interpreterContent'));
            interpreterCollapse.toggle();
        });
        
        // Si hay un proceso de análisis en curso, recargar la página cada 30 segundos
        @if(isset($assessment) && $assessment->is_processing == 1)
            // Recargar la página cada 30 segundos hasta que se complete el análisis
            setTimeout(function() {
                location.reload();
            }, 30000);
        @endif
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