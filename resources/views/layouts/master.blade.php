@extends('layouts.plane')

@section('styles')
    {{--<link rel="stylesheet" href="{{asset('plugins/datatables/media/css/dataTables.bootstrap.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('plugins/datatables/extensions/Select/css/select.bootstrap.css')}}">--}}
@endsection

@section('body')

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" title="Ir arriba" data-toggle="tooltip" data-placement="left">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </a>

{{--<header>--}}
    {{--<div class="container-fluid">--}}
        {{--<div class="row">--}}
            {{--<div class="hidden-xs hidden-md hidden-sm">--}}
                {{--@include('layouts.header')--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}

{{--</header>--}}

    @include('layouts.navbar')

    <section class="main">
        <div class="container-fluid">
            <div class="row">
                {{--<div class="col-xs-12 col-md-3 col-sm-3">--}}
                    {{--@include('layouts.nav-left')--}}
                {{--</div>--}}
                <div class="col-xs-12">
                    <div class="panel panel-info">
                        {{--<div class="panel-heading">--}}
                        <h6 class="panel-title">
                        {{--Breadcrumb--}}
                            @yield('breadcrumbs')
                        </h6>
                        {{--</div>--}}
                        <div class="panel-body">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                @include('layouts.footer')
            </div>
        </div>
    </footer>
@endsection

