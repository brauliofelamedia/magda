@extends('layouts.main')

@section('title','Evaluación')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.4/dist/jquery-steps.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.3/dist/sweetalert2.min.css">
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

    @media only screen and (max-width:768px){
        #result h2, #test1 h2, #test2 h2, #test3 h2 {
            font-size: 21px;
        }

        #result p, #test1 p, #test2 p, #test3 p {
            font-size: 15px;
        }

        .octopus {
            display: none;
        }

        #result .box-pink, #test1 .box-pink, #test2 .box-pink, #test3 .box-pink {
            padding: 30px;
            padding-bottom: 0;
        }
    }

    @media only screen and (max-width:480px){
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
                <div class="col-xl-10 col-md-8">
                    <div class="vertical-align">
                        <h2 class="fw-700 c-orange">{{$assesments['interests']['displayName']}}</h2>
                        <p>{{$assesments['interests']['instructions']}}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <img src="{{asset('assets/img/octopus-1.png')}}" class="f-right octopus b-block" alt="Octopus">
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
                            <span id="step-active"></span>/{{count($assesments['interests']['groups'][0]['items'])}}
                            <ul class="step-steps" style="display: none;">
                                @foreach($assesments['interests']['groups'][0]['items'] as $asses)
                                    <li data-step-target="step{{$asses['id']}}">{{$count}}</li>
                                @endforeach
                            </ul>
                            @php
                                $count ++;
                            @endphp

                            <div class="step-content">
                                <form action="#">
                                    @foreach($assesments['interests']['groups'][0]['items'] as $key => $asses)
                                        <div class="step-tab-panel" data-step="step{{$asses['id']}}">

                                            <h4 class="text-center">{{$assesments['interests']['groups'][0]['items'][$key]['text']}}</h4><br>

                                            @foreach($assesments['interests']['groups'][0]['items'][$key]['answers'] as $key => $answer)
                                                <div class="form-check ps-0 q-box step" data-item-id="{{$asses['id']}}">
                                                    <input class="form-check-input question__input answer" name="radio-{{$asses['id']}}" data-answer-id="{{$answer['id']}}" type="radio" id="{{$answer['id']}}">
                                                    <label class="form-check-label question__label step-btn" data-step-action="next" for="{{$answer['id']}}">{{$answer['text']}}</label>
                                                </div>
                                            @endforeach

                                        </div>
                                    @endforeach
                                </form>
                            </div>
                        </div>
                        <div class="hidden" id="step-loader" style="background-image:url('{{asset('assets/img/loader.gif')}}')"></div>
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
</script>
@endpush