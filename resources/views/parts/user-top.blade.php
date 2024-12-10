<div id="navbar">
    <div class="container">
        <div class="row">

            <div class="col-md-3 col-sm-7 col-xs-7 col-8 relative block-submenu">
                <div class="row bg-grey">
                    <div class="col-md-5 col-sm-5 col-xs-6 col-4 relative">
                        @hasanyrole(['institution','administrator','coordinator'])
                            <div class="avatar" style="background-image: url('{{@Auth::user()->avatar_url}}');"></div>
                        @else
                            <div class="avatar" style="background-image: url('{{@Auth::user()->institution->avatar_url}}');"></div>
                            <div class="avatar floating people" style="background-image: url('{{@Auth::user()->avatar_url}}');"></div>
                        @endhasanyrole
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-6 col-8">
                        <div class="vertical-align">
                            <p>Bienvenido👋🏻</p>
                            <h4 class="name c-orange fw-700">{{Auth()->user()->fullname}}</h4>
                            <form action="{{route('logout')}}" method="POST" style="display: none;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-logout">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
                <ul class="sub-menu">
                    <li><a href="{{route('users.edit',Auth()->user()->uuid)}}">Editar perfil</a></li>
                    <li>
                        <form action="{{route('logout')}}" method="POST">
                            @csrf
                            <button type="submit" class="btn-outline">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            @if(Auth::user()->hasRole('institution'))
                <div class="col-md-1 offset-md-8 col-sm-3 offset-sm-2 col-xs-3 offset-xs-2 col-3 offset-1 relative block-submenu-notify">
                    @php
                    $notification = app('notifications');
                    @endphp
                    <div class="circle-notify">
                        @if(!$notification->isEmpty())
                            <div class="notify-active"></div>
                        @endif
                        <i class="fa-solid fa-bell fa-2x"></i>
                    </div>
                    @if(!$notification->isEmpty())
                        <ul class="sub-menuu">
                            @foreach($notification as $notifi)
                                @php
                                    $name = explode(' ',$notifi->user->name);
                                @endphp
                                <li>
                                    <div class="row">
                                        <div class="col-md-12"><a href="{{route('assessments.index',$notifi->user->account_id)}}"><strong>{{$name[0]}}</strong> completó su evaluación</a></div>
                                    </div>
                                </li>
                            @endforeach
                            <li>
                                <form action="{{route('dashboard.remove.notification')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="remove" value="true">
                                    <button type="submit"><i class="fa-solid fa-check-double"></i> Borrar notificaciones</button>
                                </form>
                            </li>
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('css')
@endpush