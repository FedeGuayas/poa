<nav class="navbar navbar-default navbar-inverse navbar-fixed-top">

    <div class="container-fluid">

        <!-- Logo y boton de menu para mobiles -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"
                    aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{url('/')}}">PAC-POA</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse">

            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="{{url('/')}}"><i class="fa fa-home" aria-hidden="true"></i>
                        <span class="visible-lg-inline-block">
                            Inicio
                        </span>
                        <span class="sr-only">(current)</span>
                    </a>
                </li>

                @permission( 'ver-esigef')
                <li role="presentation">
                    <a href="{{route('poa')}}"><i class="fa fa-eye" aria-hidden="true"></i>
                        <span class="visible-lg-inline-block">
                            ESIGEF
                        </span>
                    </a>
                </li>
                @endpermission

                @if (Auth::check())
                    @if (Auth::user()->hasRole(['administrador','root','responsable-poa']))
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-secret" aria-hidden="true"></i>
                                <span class="visible-lg-inline-block">
                                    Accesos
                                </span>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('admin.users.index')}}">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                        Usuarios
                                    </a>
                                </li>
                                @permission('admin-roles')
                                <li>
                                    <a href="{{route('admin.roles.index')}}">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                        Roles
                                    </a>
                                </li>
                                @endpermission
                                @permission('admin-permisos')
                                <li>
                                    <a href="{{route('admin.permissions.index')}}">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                        Permisos
                                    </a>
                                </li>
                                @endpermission
                            </ul>
                        </li>

                    @endif
                @endif

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                       aria-expanded="false"><i class="fa fa-cogs" aria-hidden="true"></i>
                        <span class="visible-lg-inline-block">
                            Configuración
                        </span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {{--<li role="presentation"><a href="{{route('configurationGet')}}"> Configuración</a></li>--}}

                        @permission('importa-presupuesto')
                        <li role="presentation">
                            <a href="{{route('loadPresupuesto')}}"><i class="fa fa-upload" aria-hidden="true"></i>
                                Importar Presupuesto <i class="fa fa-money text-success" aria-hidden="true"></i>
                            </a>
                        </li>
                        @endpermission

                        @permission('importa-esigef')
                        <li role="presentation">
                            <a href="{{route('loadPOA')}}"><i class="fa fa-upload" aria-hidden="true"></i>
                                Cargar-ESIGEF
                            </a>
                        </li>
                        @endpermission

                        @permission('hacer-cierre')
                        <li role="presentation">
                            <a href="{{route('admin.historico.cierre')}}"><i class="fa fa-calendar-times-o" aria-hidden="true"></i>
                                Cierre
                            </a>
                        </li>
                        @endpermission

                        <li role="separator" class="divider"></li>

                        <li role="presentation">
                            <a href="{{route('admin.areas.index')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                Direcciones
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="{{route('admin.departamentos.index')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                Coordinaciones
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="{{route('admin.programas.index')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                Programas
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="{{route('admin.actividades.index')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                Actividades
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="{{route('admin.workers.index')}}"><i class="fa fa-users" aria-hidden="true"></i>
                                Trabajadores
                            </a>
                        </li>

                        @permission('admin-items')
                        <li role="separator" class="divider"></li>
                        <li role="presentation">
                            <a href="{{route('admin.items.index')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                Items
                            </a>
                        </li>
                        @endpermission

                    </ul>

                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        POA FDG <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">

                        @permission('planifica-poa')
                        <li role="presentation">
                            <a href="{{route('poaFDG')}}"><i class="fa fa-tasks" aria-hidden="true"></i>
                                Planificación
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="{{route('admin.ingresos.index')}}"><i class="fa fa-usd" aria-hidden="true"></i>
                                Ingresos Extras
                            </a>
                        </li>
                        @endpermission

                        <li role="presentation"><a href="{{route('admin.poa.index')}}"><i class="fa fa-money" aria-hidden="true"></i>
                                Presupuesto
                            </a>
                        </li>

                    </ul>

                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        PAC
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">

                        @permission('planifica-pac')
                        <li role="presentation"><a href="{{route('indexPlanificacion')}}"><i class="fa fa-tasks" aria-hidden="true"></i>
                                Planificación
                            </a>
                        </li>
                        @endpermission

                        @permission('gestion-procesos')
                        <li role="presentation">
                            <a href="{{route('admin.pacs.index')}}"><i class="fa fa-list-ol" aria-hidden="true"></i>
                                Procesos
                            </a>
                        </li>
                        <li role="presentation"><a href="{{route('admin.gestion.index')}}"><i class="fa fa-list-ol" aria-hidden="true"></i>
                                Gestiones
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-recycle" aria-hidden="true"></i>
                        <span class="visible-lg-inline-block">
                            Reformas
                        </span>
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">
                        <li role="presentation">
                            <a href="{{route('admin.reformas.index')}}">
                                Reformas
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                       aria-expanded="false"><i class="fa fa-file-text" aria-hidden="true"></i> <span
                                class="visible-lg-inline-block">
                        Reportes</span><span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('admin.reportes.resumen_mensual')}}"> Resumen mensual</a></li>
                        <li><a href="{{route('admin.historico.index')}}"> Histórico</a></li>
                    </ul>
                </li>

            </ul>
            {{--buscador--}}
            {{--<form class="navbar-form navbar-left">--}}
            {{--<div class="form-group">--}}
            {{--<input type="text" class="form-control" placeholder="Buscar">--}}
            {{--</div>--}}
            {{--<button type="submit" class="btn btn-default">Submit</button>--}}
            {{--</form>--}}

            {{--navegacion a la derecha--}}
            <ul class="nav navbar-nav ">

                <!--MENU DROPDOWN DE USUARIOS-->
                @if (Auth::guest())
                    <li>
                        <a href="{{ url('/login') }}">
                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                            Entrar
                        </a>
                    </li>
                @else
                <!-- User Account  -->
                    <li class="dropdown">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <i class="fa fa-user-circle"></i>
                        {{--<img src="dist/img/avatar5.png" class="user-image" alt="User Image">--}}
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="visible-lg-inline-block">
                                {{ Auth::user()->name }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </a>

                        <ul class="dropdown-menu">
                            <!-- Nombre de usuario -->
                            <div class="profile-usertitle">
                                <div class="profile-usertitle-name">
                                    {{--{{ Auth::user()->name }}--}}
                                </div>
                                <div class="profile-usertitle-access">
                                    {{--{{ Auth::user()->roles }}--}}
                                </div>
                            </div>
                            <!-- END Nombre de Usuario -->

                            <!-- Botones -->
                            {{--<div class="profile-userbuttons">--}}
                                <li>
                                <a href="{{route('user.password.edit',Auth::user())}}">
                                    <i class="fa fa-key"></i>
                                    Cambiar Contraseña
                                </a>
                                </li>
                                {{--<button type="button" class="btn btn-danger btn-sm">Message</button>--}}
                            {{--</div>--}}
                            <!-- END Botones -->

                            @if (Auth::check())
                                <li>
                                    <a href="{{route('logout')}}">
                                        <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                                        Salir
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif

            </ul>

        </div><!-- /.navbar-collapse -->

    </div><!-- /.container-fluid -->

</nav>