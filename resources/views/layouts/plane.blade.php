<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="Sistema para la Gestion Deportiva de La Federacion Deportiva del Guayas" name="description"/>
    <meta content="Ing. Hector Alain Alvarez Gomez" name="author"/>
    <title>
        @hasSection ('title')
        PAC-POA | @yield('title')
        @else
            PAC-POA
        @endif
    </title>

    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset("plugins/bootstrap/css/bootstrap.min.css") }}"/>
    <!-- Bootstrap-Select CSS-->
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-select/css/bootstrap-select.min.css')}}">
    <!-- font-awesome CSS-->
    <link rel="stylesheet" href="{{ asset("plugins/font-awesome/css/font-awesome.css") }}"/>
    <!-- SweetAlert CSS-->
    <link rel="stylesheet" href="{{ asset("plugins/sweetalert-master/dist/sweetalert.css") }}"/>
    <!-- DataTables CSS-->
    <link rel="stylesheet" href="{{asset('plugins/datatables/datatables.min.css')}}">
    {{--<link rel="stylesheet" href="{{asset('plugins/datatables/media/css/dataTables.bootstrap.css')}}">--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/datatables/extensions/Select/css/select.bootstrap.css')}}">--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/datatables/extensions/Buttons/css/buttons.dataTables.css')}}">--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/datatables/extensions/Buttons/css/buttons.bootstrap.css')}}">--}}

    {{--Boostrap DateTimePicker--}}
    <link rel="stylesheet" href="{{ asset("plugins/bDateTimePicker/css/bootstrap-datetimepicker.css") }}" />

    {{--Chart JS--}}
    <script src="{{ asset("plugins/chartjs/Chart.min.js") }}" type="text/javascript"></script>

    {{--Pace JS--}}
    {{--<script src="{{asset('plugins/pace/pace.min.js')}}"></script>--}}
    {{--<link href="{{asset('plugins/pace/themes/pink/pace-theme-loading-bar.css')}}" rel="stylesheet" />--}}

    <!-- Boton ir arriba CSS-->
    <link rel="stylesheet" href="{{ asset("css/myStyles.css") }}"/>
    @yield('styles')

            <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
{{--CONTENIDO--}}
@yield('body')
{{--END CONTENIDO--}}

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{ asset("plugins/jquery/jquery-3.2.0.js") }}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{ asset("plugins/bootstrap/js/bootstrap.min.js") }}"></script>
<!-- Bootstrap select -->
<script src="{{ asset("plugins/bootstrap-select/js/bootstrap-select.min.js") }}"></script>
<script src="{{ asset("plugins/bootstrap-select/js/i18n/defaults-es_ES.min.js") }}"></script>
<!-- SweetAlert -->
<script src="{{ asset("plugins/sweetalert-master/dist/sweetalert.min.js") }}"></script>

<!-- Scripts DataTables -->
<script src="{{asset('plugins/datatables/datatables.min.js')}}"></script>
{{--<script src="{{asset('plugins/datatables/media/js/jquery.dataTables.min.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/media/js/dataTables.bootstrap.min.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Buttons/js/dataTables.buttons.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Buttons/js/buttons.bootstrap.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Buttons/js/buttons.flash.min.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Buttons/js/buttons.html5.min.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Buttons/js/buttons.print.min.js')}}"></script>--}}
{{--<script src="{{asset('plugins/datatables/extensions/Select/js/dataTables.select.js')}}"></script>--}}


{{--Boostrap DateTimePicker--}}
<script src="{{ asset("plugins/moment/min/moment-with-locales.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("plugins/bDateTimePicker/js/bootstrap-datetimepicker.min.js") }}" type="text/javascript"></script>


<!-- Scripts app -->
<script src="{{ asset("js/myScripts.js") }}"></script>

@yield('scripts')

</body>
</html>

