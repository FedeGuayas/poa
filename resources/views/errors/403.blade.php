@extends('layouts.plane')

@section('body')

    <div class="container center-block">

        <h1 class="page-header text-capitalize text-danger"> 403</h1>

        <div class="error-content">
            <h3><i class="fa fa-2x fa-ban text-danger"></i> Oops! Acceso Denegado.</h3>

            <h3 class="text-danger">
               No tiene los permisos suficientes para realizar la acción. Ud puede <a href="javascript:history.go(-1)"><strong><i class="fa fa-arrow-left"></i> regresar atras</strong></a>
                o ir al  <a href="{{route('inicio')}}"><strong> <i class="fa fa-home"></i> inicio</strong></a>

            </h3>

        </div>
        <!-- /.error-content -->
    </div>
    <!-- /.error-page -->
    </div>

@endsection