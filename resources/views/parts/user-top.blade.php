<div id="navbar">
    <div class="container">
        <div class="row">
            <div class="col-xl-11 col-sm-9 col-10">
                <div class="row">
                    <div class="col-md-2 col-4">
                        <div class="avatar" style="background-image: url('{{asset('assets/img/avatar.png')}}');"></div>
                    </div>
                    <div class="col-md-10 col-8">
                        <div class="vertical-align">
                            <p>Bienvenido👋🏻</p>
                            <h4 class="name c-orange fw-700">{{Auth()->user()->name}}</h4>
                            <form action="{{route('logout')}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-logout">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-1 col-md-1 col-2">
                <img src="{{asset('assets/img/notification.png')}}" class="f-right b-block notification-icon" alt="Notification">
            </div>
        </div>
    </div>
</div>