<div id="navbar">
    <div class="container">
        <div class="row">

            <div class="col-md-3 col-sm-7 col-xs-7 col-8 relative block-submenu">
                <div class="row bg-grey">
                    <div class="col-md-5 col-sm-5 col-xs-6 col-5 relative">
                        <div class="avatar" style="background-image: url('{{@Auth::user()->institution->avatar_url}}');"></div>
                        <div class="avatar floating people" style="background-image: url('{{@Auth::user()->avatar_url}}');"></div>
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-6 col-7">
                        <div class="vertical-align">
                            <p>Bienvenido👋🏻</p>
                            <h4 class="name c-orange fw-700">{{Auth()->user()->name}}</h4>
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

            <div class="col-md-1 offset-md-8 col-sm-3 offset-sm-2 col-xs-3 offset-xs-2 col-3 offset-1">
                <img src="{{asset('assets/img/notification.png')}}" class="f-right b-block notification-icon" alt="Notification">
            </div>
        </div>
    </div>
</div>

@push('css')
@endpush