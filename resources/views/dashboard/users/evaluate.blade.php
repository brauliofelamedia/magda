@extends('layouts.main')

@section('title','Evaluación')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.4/dist/jquery-steps.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.3/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    .step-tab-panel {
        display: none;
    }

    #step-loader {
        opacity: 1!important;;
        background-size: 160%;
        width: 50px;
        height: 50px;
        background-repeat: no-repeat;
        background-position: center;
        margin: 0 auto;
    }

    #step-loader.hidden {
        opacity: 0!important;
    }

    .step-tab-panel.active {
        display: block;
    }

    .step-footer {
        text-align: center;
    }

    .step-app>.step-content {
        border: 0;
        padding: 0;
    }

    #octopus {
        position: absolute;
        left: -110px;
        width: 290px;
        bottom: -80px;
    }

    .grey-content {
        background-color: #ececec;
        padding: 25px;
        border-radius: 10px;
        font-size: 16px;
        line-height: 1.3em;
        margin-bottom: 20px;
    }

    @media only screen and (max-width:768px){

        #octopus {
            position: absolute;
            width: 170px;
            top: -20px;
            left: inherit;
            right: 0;
        }

        #result h2, #test1 h2, #test2 h2, #test3 h2 {
            font-size: 21px;
        }

        #result p, #test1 p, #test2 p, #test3 p {
            font-size: 15px;
        }

        #result .box-pink, #test1 .box-pink, #test2 .box-pink, #test3 .box-pink {
            padding: 30px;
            padding-bottom: 0;
        }
    }

    @media only screen and (max-width:480px){

        #octopus {
            width: 130px;
            top: -70px;
            left: inherit;
            right: 0;
        }
        
        #result .box-pink, #test1 .box-pink, #test2 .box-pink, #test3 .box-pink {
            padding: 30px;
            padding-bottom: 0;
        }

        .octopus {
            display: none;
        }

        #result h2, #test1 h2, #test2 h2, #test3 h2 {
            font-size: 25px;
        }

        #result p, #test1 p, #test2 p, #test3 p {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')

<div class="container" id="test1">
    <div class="row">
        <div class="col-xl-11">
            <div class="row">
                <div class="col-xl-12 col-md-8">
                    <div class="vertical-align">
                        @php
                            $isMultiArray = isset($assesments['interests']) || isset($assesments['cognitive']);
                            $sections = $isMultiArray ? $assesments : ['default' => $assesments];
                        @endphp
                        @foreach($sections as $sectionKey => $section)
                            <h2 class="fw-700 c-orange section-title" data-section-id="{{$sectionKey}}" style="display: none;">{{$section['displayName'] ?? 'Sección'}}</h2>
                            <p class="section-instructions" data-section-id="{{$sectionKey}}" style="display: none;">{{$section['instructions'] ?? ''}}</p>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-2 col-md-4" style="display: none;">
                    <img src="{{asset('assets/img/detail.png')}}" class="f-right octopus b-block" alt="Octopus">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12 text-center">
            <div class="box-pink">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="step-app" id="steps">
                            @php
                                $id = request()->route('id');
                                $token = request()->route('token');
                                $userId = request()->route('userId');
                                $count = 1;
                            @endphp
                            <span id="step-active"></span>/
                            <span id="step-total">
                                {{ array_sum(array_map(function($section) {
                                    $total = 0;
                                    foreach ($section['groups'] ?? [] as $group) {
                                        $total += count($group['items'] ?? []);
                                    }
                                    return $total;
                                }, $sections)) }}
                            </span>
                            <ul class="step-steps" style="display: none;">
                                @foreach($sections as $sectionKey => $section)
                                    @foreach(($section['groups'] ?? []) as $group)
                                        @foreach(($group['items'] ?? []) as $asses)
                                            <li data-step-target="step{{$asses['id']}}">{{$count}}</li>
                                            @php $count++; @endphp
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </ul>
                            <div class="step-content">
                                <form action="#">
                                    @foreach($sections as $sectionKey => $section)
                                        @foreach(($section['groups'] ?? []) as $group)
                                            @foreach(($group['items'] ?? []) as $key => $asses)
                                                <div class="step-tab-panel" data-step="step{{$asses['id']}}" data-section-id="{{$sectionKey}}">
                                                    {{var_dump($asses['id'])}}
                                                    <h4 class="text-center">{{$asses['text']}}</h4><br>
                                                    @php
                                                        $preguntasConContenido = [
                                                            [
                                                                'id' => 'COG_9',
                                                                'contenido' => 'Muchas especies de aves pueden considerarse, en parte o exclusivamente, depredadoras. Tomado literalmente, el término "ave de presa" tiene un significado amplio que incluye muchas aves que cazan y se alimentan de animales y también las aves que se alimentan de pequeños insectos. En la ornitología, la definición de "ave de presa" tiene un significado más concreto: aves que tienen muy buena vista para encontrar comida, fuertes patas para sujetar la comida y un fuerte pico curvo para desgarrar la comida. La mayoría de las aves de presa también tienen fuertes garras curvadas para atrapar o matar a sus presas. Un ejemplo de esta diferencia en la definición, la definición más restringida excluye cigüeñas y gaviotas que pueden comer peces bastante grandes, en parte porque estas aves atrapan y matan totalmente con sus picos y, de modo similar, a los skuas que se alimentan de pájaros, a los pingüinos que comen peces y a las cucaburras que comen vertebrados, todos están excluídos. Las aves de presa, en general, se alimentan de los vertebrados, que son normalmente bastante grandes en relación al tamaño del ave. La mayoría también comen carroña, al menos ocasionalmente, y los buitres y cóndores comen carroña como su principal fuente de alimento. Muchas especies de aves rapaces son consideradas depredadoras.'
                                                            ],
                                                            [
                                                                'id' => 'COG_7',
                                                                'contenido' => 'Muchas especies de aves pueden considerarse, en parte o exclusivamente, depredadoras. Tomado literalmente, el término "ave de presa" tiene un significado amplio que incluye muchas aves que cazan y se alimentan de animales y también las aves que se alimentan de pequeños insectos. En la ornitología, la definición de "ave de presa" tiene un significado más concreto: aves que tienen muy buena vista para encontrar comida, fuertes patas para sujetar la comida y un fuerte pico curvo para desgarrar la comida. La mayoría de las aves de presa también tienen fuertes garras curvadas para atrapar o matar a sus presas. Un ejemplo de esta diferencia en la definición, la definición más restringida excluye cigüeñas y gaviotas que pueden comer peces bastante grandes, en parte porque estas aves atrapan y matan totalmente con sus picos y, de modo similar, a los skuas que se alimentan de pájaros, a los pingüinos que comen peces y a las cucaburras que comen vertebrados, todos están excluídos. Las aves de presa, en general, se alimentan de los vertebrados, que son normalmente bastante grandes en relación al tamaño del ave. La mayoría también comen carroña, al menos ocasionalmente, y los buitres y cóndores comen carroña como su principal fuente de alimento. Muchas especies de aves rapaces son consideradas depredadoras.'
                                                            ],
                                                            [
                                                                'id' => 'COG_31',
                                                                'contenido' => 'De las cuatro respuestas sugeridas selecciona, por favor, la palabra o frase que crees que tiene el significado MÁS PARECIDO a la indicada.'
                                                            ],
                                                            [
                                                                'id' => 'COG_32',
                                                                'contenido' => 'De las cuatro respuestas sugeridas selecciona, por favor, la palabra o frase que crees que tiene el significado MÁS PARECIDO a la indicada.'
                                                            ],
                                                            [
                                                                'id' => 'COG_14',
                                                                'contenido' => 'Los orígenes de Internet se remontan a la investigación encargada por el gobierno de los Estados Unidos en la década de los 60 para construir una comunicación robusta, tolerante a fallos de comunicación a través de redes informáticas. Este trabajo, combinado con el trabajo del Reino Unido y Francia, llevó a la red primaria precursora, ARPANET, en Estados Unidos. Un documento de 1980 alude a "ARPA Internet". La interconexión de las redes académicas regionales en los años 80 marcó el comienzo de la transición al moderno Internet. Desde principios de 1990, la red ha experimentado un crecimiento exponencial sostenido en tanto que las generaciones de computadoras institucionales, personales y los teléfonos se conectaron a ella.'
                                                            ],
                                                            [
                                                                'id' => 'COG_11',
                                                                'contenido' => 'A pesar del meteórico crecimiento de la tasa del producto interior bruto (PIB) del país (alrededor del 9%), la pobreza en la India sigue siendo generalizada; especialmente en las áreas rurales donde vive el 70% de los 1.2 mil millones de la población. Es una de las economías de más rápido crecimiento en el mundo, sin embargo sus riquezas apenas se redistribuyen entre la población. Gasta aproximadamente el 1% de su PIB en salud, que es la mitad del gasto de China, quien ya está planificando un incremento entre el 3 y el 4%.'
                                                            ],
                                                            [
                                                                'id' => 'COG_10',
                                                                'contenido' => 'A pesar del meteórico crecimiento de la tasa del producto interior bruto (PIB) del país (alrededor del 9%), la pobreza en la India sigue siendo generalizada; especialmente en las áreas rurales donde vive el 70% de los 1.2 mil millones de la población. Es una de las economías de más rápido crecimiento en el mundo, sin embargo sus riquezas apenas se redistribuyen entre la población. Gasta aproximadamente el 1% de su PIB en salud, que es la mitad del gasto de China, quien ya está planificando un incremento entre el 3 y el 4%.'
                                                            ],
                                                            [
                                                                'id' => 'COG_34',
                                                                'contenido' => 'De las cuatro respuestas sugeridas selecciona, por favor, la palabra o frase que crees que tiene el significado MÁS ALEJADO de la indicada.'
                                                            ],
                                                            [
                                                                'id' => 'COG_33',
                                                                'contenido' => 'De las cuatro respuestas sugeridas selecciona, por favor, la palabra o frase que crees que tiene el significado MÁS ALEJADO de la indicada.'
                                                            ],
                                                            [
                                                                'id' => 'COG_13',
                                                                'contenido' => 'Los orígenes de Internet se remontan a la investigación encargada por el gobierno de los Estados Unidos en la década de los 60 para construir una comunicación robusta, tolerante a fallos de comunicación a través de redes informáticas. Este trabajo, combinado con el trabajo del Reino Unido y Francia, llevó a la red primaria precursora, ARPANET, en Estados Unidos. Un documento de 1980 alude a "ARPA Internet". La interconexión de las redes académicas regionales en los años 80 marcó el comienzo de la transición al moderno Internet. Desde principios de 1990, la red ha experimentado un crecimiento exponencial sostenido en tanto que las generaciones de computadoras institucionales, personales y los teléfonos se conectaron a ella.'
                                                            ],
                                                            [
                                                                'id' => 'COG_5',
                                                                'contenido' => 'Aunque todavía no está reconocido por la Asociación Médica Internacional (IMA) como un trastorno diagnosticable, la adicción a los vídeojuegos es un problema muy real para muchas personas. Estudios recientes sugieren que entre el 6 y el 15 por ciento de los jugadores muestran signos que podrían ser caracterizados como una adicción. Aunque este transtorno puede tener consecuencias significativas para todas aquellas personas que lo padecen, sus signos y síntomas pueden, a veces, ser difícilmente reconocidos.'
                                                            ],
                                                            [
                                                                'id' => 'COG_4',
                                                                'contenido' => 'Aunque todavía no está reconocido por la Asociación Médica Internacional (IMA) como un trastorno diagnosticable, la adicción a los vídeojuegos es un problema muy real para muchas personas. Estudios recientes sugieren que entre el 6 y el 15 por ciento de los jugadores muestran signos que podrían ser caracterizados como una adicción. Aunque este transtorno puede tener consecuencias significativas para todas aquellas personas que lo padecen, sus signos y síntomas pueden, a veces, ser difícilmente reconocidos.'
                                                            ],
                                                            [
                                                                'id' => 'COG_1',
                                                                'contenido' => 'Por lo general, la gripe aviar (H5N1) no se transmite de las aves a los humanos. Sin embargo, en los últimos diez años ha habido cientos de casos de gripe aviar en los humanos, y, los científicos creen que, si hay una forma de que se contagie de persona a persona, podría tratarse de una gripe pandémica. La mayor parte de los casos de contagio de H5N1 se cree que han ocurrido como resultado del contacto directo o cercano con aves de corral enfermas o infectadas. Eso es porque no hay una inmunidad natural de la gripe aviar en los humanos. Nuestro cuerpo no puede desarrollar anticuerpos y, por lo general, se requiere hospitalización. Debido a esto, cualquier persona está en riesgo de contraer gripe aviar y eso es algo que aumenta la preocupación acerca de una gripe pandémica.'
                                                            ],
                                                            [
                                                                'id' => 'COG_2',
                                                                'contenido' => 'Por lo general, la gripe aviar (H5N1) no se transmite de las aves a los humanos. Sin embargo, en los últimos diez años ha habido cientos de casos de gripe aviar en los humanos, y, los científicos creen que, si hay una forma de que se contagie de persona a persona, podría tratarse de una gripe pandémica. La mayor parte de los casos de contagio de H5N1 se cree que han ocurrido como resultado del contacto directo o cercano con aves de corral enfermas o infectadas. Eso es porque no hay una inmunidad natural de la gripe aviar en los humanos. Nuestro cuerpo no puede desarrollar anticuerpos y, por lo general, se requiere hospitalización. Debido a esto, cualquier persona está en riesgo de contraer gripe aviar y eso es algo que aumenta la preocupación acerca de una gripe pandémica.'
                                                            ],
                                                            [
                                                                'id' => 'COG_16',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                            [
                                                                'id' => 'COG_17',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                            [
                                                                'id' => 'COG_19',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                            [
                                                                'id' => 'COG_18',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                            [
                                                                'id' => 'COG_21',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                            [
                                                                'id' => 'COG_20',
                                                                'contenido' => 'Cada problema consiste en tres frases. Según las dos primeras frases, la tercera puede ser verdadera, falsa o desconocida'
                                                            ],
                                                        ];
                                                    @endphp

                                                    @foreach($preguntasConContenido as $preguntaContenido)
                                                        @if($asses['id'] == $preguntaContenido['id'])
                                                            <p class="grey-content">{{ $preguntaContenido['contenido'] }}</p>
                                                        @endif
                                                    @endforeach
                                                    @if($sectionKey == 'cognitive')
                                                        @php
                                                            $cognitiveIndex = array_search($asses, array_merge(...array_column($section['groups'], 'items'))) + 1;
                                                        @endphp
                                                        @if($cognitiveIndex >= 1 && $cognitiveIndex <= 5)
                                                            <div class="text-center mb-4">
                                                                <img src="{{asset('assets/img/cognitive/cogni-'.$cognitiveIndex.'.png')}}" alt="Cognitive Image {{$cognitiveIndex}}" class="img-fluid">
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @foreach($asses['answers'] as $key => $answer)
                                                        @php
                                                            $original = $answer['text'];
                                                            $new_string = str_replace("probablemente", "probable", $original);
                                                        @endphp
                                                        <div class="form-check ps-0 q-box step" data-item-id="{{$asses['id']}}">
                                                            <input class="form-check-input question__input answer" name="radio-{{$asses['id']}}" data-answer-id="{{$answer['id']}}" type="radio" id="{{$answer['id']}}">
                                                            <label class="form-check-label question__label step-btn" data-step-action="next" for="{{$answer['id']}}">{{$new_string}}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </form>
                            </div>
                        </div>
                        <div class="hidden" id="step-loader" style="background-image:url('{{asset('assets/img/loader.gif')}}')"></div>
                        <img src="{{asset('assets/img/detail.png')}}" id="octopus" class="animate__animated octopus" alt="Octopus">
                    </div>
                </div>
            </div>
            <a href="{{route('dashboard.welcome')}}" class="btn btn-danger btn-xs" style="margin-top: 20px;">Cancelar y regresar</a>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.4/dist/jquery-steps.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.3/dist/sweetalert2.all.min.js"></script>
<script>
    //Obtenemos la respuesta y enviamos al API
    var response = {};

    $('.answer').on('click', function() {
        $('#step-loader').removeClass('hidden');
        $('.answer').attr('disabled',true);
        var itemId = $(this).closest('.step').data('item-id');
        var answerId = $(this).data('answer-id');
        /*steps_api.next();*/
        var newKey = answerId.slice(0, -1) + parseInt(getLatestDigit(answerId));
        response[newKey] = parseInt(getLatestDigit(answerId));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Aquí puedes hacer la llamada AJAX para enviar el JSON al servidor
        $.ajax({
            url: '{{route('assessments.update')}}',
            type: 'POST',
            data: {
                responses: response,
                token: '{{$token}}',
                id: '{{$id}}'
            },
            success: function(response) {

                if(response.success){
                    steps_api.next();
                    $('#step-loader').addClass('hidden');
                    $('.answer').attr('disabled',false);
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    });

    function getLatestDigit(string) {
        if (string) {
            return string.charAt(string.length - 1);
        } else {
            return null;
        }
    }

    //Formulario steps
    var steps = $('#steps').steps({
        onChange: function (currentIndex, newIndex, stepDirection) {
            $('#step-active').text(newIndex + 1);
            const currentSectionId = $('.step-tab-panel').eq(newIndex).data('section-id');
            updateStepTotal(currentSectionId);
            updateSectionVisibility(newIndex + 1);
            return true;
        },
        onFinish: function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{route('assessments.close')}}',
                type: 'POST',
                data: {
                    token: '{{$token}}',
                    id: '{{$id}}'
                },
                success: function(response) {

                    if(response.success){
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'La evaluación se contesto correctamente.',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        setTimeout(function() {
                            var id = '{{$id}}';
                            var routeUrl = "{{ route('assessments.finish', ':id') }}";
                            routeUrl = routeUrl.replace(':id', id);
                            window.location.href = routeUrl;
                        }, 2000);
                    }
                },
                error: function(error) {
                    console.log('Error:', error);
                }
            });
        
        },
    });

    steps_api = steps.data('plugin_Steps');

    //Animation class random
    $(document).ready(function() {
        $('.step-btn').on('click', function() {
            var img1 = "{{ asset('assets/img/edit.png') }}";
            var img2 = "{{ asset('assets/img/finish.png') }}";
            var img3 = "{{ asset('assets/img/detail.png') }}";
            const img = [img1, img3];
            const clases = ['animate__bounce', 'animate__fadeInUpBig', 'animate__fadeInLeftBig', 'animate__fadeInBottomRight', 'animate__fadeInTopRight', 'animate__jello', 'animate__jello', 'animate__heartBeat', 'animate__rubberBand', 'animate__swing'];

            const indiceAleatorio = Math.floor(Math.random() * clases.length);
            const imgAleatorio = Math.floor(Math.random() * img.length);

            const claseAleatoria = clases[indiceAleatorio];
            const imgAleatoria = img[imgAleatorio];

            const $elemento = $('#octopus');

            function anadirClase() {
                $elemento.addClass('octopus');
            }

            // Cambiar fotos
            $elemento.attr('src', imgAleatoria);

            $elemento.removeClass();
            $elemento.addClass('animate__animated');
            $elemento.addClass(claseAleatoria);
            setTimeout(anadirClase, 2000);
        });
    });

    // Update section title and instructions visibility based on the current step
    function updateSectionVisibility(currentStep) {
        let cumulativeCount = 0;
        let currentSectionId = null;

        // Determine the current section based on the step number
        $('.step-tab-panel').each(function(index) {
            const sectionId = $(this).data('section-id');
            if (sectionId !== currentSectionId) {
                currentSectionId = sectionId;
                cumulativeCount = 0; // Reset count for the new section
            }
            cumulativeCount++;
            if (index + 1 === currentStep) {
                return false; // Break the loop when the current step is found
            }
        });

        // Hide all section titles and instructions
        $('.section-title, .section-instructions').hide();

        // Show the title and instructions for the current section
        if (currentSectionId) {
            $(`.section-title[data-section-id="${currentSectionId}"], .section-instructions[data-section-id="${currentSectionId}"]`).show();
        }
    }

    // Update the total steps dynamically based on the current section
    function updateStepTotal(currentSectionId) {
        const totalSteps = $(`.step-tab-panel[data-section-id="${currentSectionId}"]`).length;
        $('#step-total').text(totalSteps);
    }

    // Initialize visibility on page load
    $(document).ready(function() {
        const initialSectionId = $('.step-tab-panel').first().data('section-id');
        updateStepTotal(initialSectionId);
        const initialStep = 1; // Start with the first step explicitly
        updateSectionVisibility(initialStep); // Ensure the first step's instructions are shown
    });
</script>
@endpush