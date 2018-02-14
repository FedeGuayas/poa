<nav class="col-sm-12 nav-izq">

    @if (Auth::check())
            <!-- Imagen Usuario  -->
    <div class="profile-userpic">
        <img src="{{asset('images/users/head-196541_640.jpg')}}" class="img-responsive" alt="">
    </div>
    <!-- END Imagen usuario -->
    <!-- Nombre de usuario -->
    <div class="profile-usertitle">
        <div class="profile-usertitle-name">
            Hector Alvarez
        </div>
        <div class="profile-usertitle-access">
            Administrador
        </div>
    </div>
    <!-- END Nombre de Usuario -->
    <!-- Botones -->
    <div class="profile-userbuttons">
        <button type="button" class="btn btn-primary btn-sm">Cambiar Contraseña</button>
        {{--<button type="button" class="btn btn-danger btn-sm">Message</button>--}}
    </div>
    <!-- END Botones -->
    @endif
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href="#"> <span class="glyphicon glyphicon-home"></span> Inicio </a></li>
        <li><a href="#"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Entrar </a></li>
        @if (Auth::check())
            <li><a href="#"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Salir </a></li>
        @endif
        {{--<li> <a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Usuario </a> </li>--}}
        <li><a href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> </span> Ayuda </a></li>
    </ul>
</nav>