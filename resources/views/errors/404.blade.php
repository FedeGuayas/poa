@extends('layouts.plane')

@section('body')

        <div class="container">

                <h1 class="page-header text-capitalize text-warning"> 404</h1>

                <div class="error-content">
                    <h3><i class="fa fa-2x fa-warning text-warning"></i> Oops! Página no encontrada.</h3>

                    <p>
                        No se pudo encontrar la página que estaba buscando.
                        Ud puede <a href="javascript:history.go(-1)"><strong><i class="fa fa-arrow-left"></i> regresar atras</strong></a>
                        o ir al  <a href="{{route('inicio')}}"><strong> <i class="fa fa-home"></i> inicio</strong></a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </div>

@endsection
