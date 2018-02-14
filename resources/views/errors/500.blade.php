@extends('layouts.plane')

@section('body')

        <div class="container">

                <h1 class="page-header"> 500</h1>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Vaya! , parece que algo ha ido mal.</h3>

                    <p>
                        Ha ocurrido un error interno en el servidor
                        Ud puede <a href="javascript:history.go(-1)">regresar atras</a> o ir al  <a href="#!">inicio</a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </div>

@endsection
