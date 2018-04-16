@extends('layouts.plane')
@section('title','Liberar Recursos')

@section('styles')
    <style>
        body {
            padding-top: 5px;
            /*margin-bottom: 160px;*/
        }
    </style>
@endsection

@section('body')

    <div class="container">
        {!! Form::open(['route' =>['pac.permitReform',$pac->id], 'method'=>'put','id'=>'permit-reform-form']) !!}
        <div class="row">
            <div class="page-header">
                <h1>Liberar monto que será utilizado para reforma
                </h1>
                @include('alert.alert')
                @include('alert.request')
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="user-from" class="control-label">De:</label>
                    <input type="text" class="form-control" id="user-from" readonly name="user-from"
                           value="{{$pac->worker->nombres.' '.$pac->worker->apellidos.' ( '.$pac->worker->email.' )'}}">
                    <small id="userFromHelpBlock" class="form-text text-muted">
                        Usuario responsable del PAC.
                    </small>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('valor_disponible','Disponible:') !!}
                    <div class="input-group has-success">
                        <span class="input-group-addon"><i class="fa fa-dollar text-succes"></i></span>
                        {!! Form::text('valor_disponible',number_format($pac->disponible,'2','.',' '),['class'=>'form-control tip','data-placement'=>'top','title'=>'Valor disponible','id'=>'valor_disponible','required','readonly']) !!}

                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('reform_value','Liberar:') !!}
                    <div class="input-group has-warning">
                        <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                        {!! Form::number('reform_value',null,['step' => '0.01','min' => '0','class'=>'form-control tip','data-placement'=>'top','title'=>'Valor a liberar Inc. IVA','placeholder'=>'0.00','id'=>'reform_value','required']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="users-to" class="control-label">Para:</label>
            <input type="text" class="form-control" id="users-to" name="user-to" readonly
                   value="@foreach ($correo_para as $worker){{$worker}};@endforeach">
            <small id="userToHelpBlock" class="form-text text-muted">
                Usuarios con permisos de realizar reformas y el jefe de área.
            </small>
        </div>
        <div class="form-group">
            <label for="message-note" class="control-label">Nota:</label>
            <textarea class="form-control" rows="5" id="message-note" name="message-note" readonly>Estimada(o), se informa que se acaban de liberar recursos para reforma , del proceso PAC: {{$pac->concepto}}, CÓDIGO POA: {{$pac->area_item->item->cod_programa .'-'.$pac->area_item->item->cod_actividad.'-'.$pac->area_item->item->cod_item}}</textarea>
            <small id="messageTextBlock" class="form-text text-muted">
                Nota obligatoria
            </small>
        </div>
        <div class="form-group">
            <label for="message-text" class="control-label">Mensaje:</label>
            <textarea class="form-control" id="message-text" name="message-text"
                      style="text-transform: uppercase"></textarea>
            <small id="messageTextBlock" class="form-text text-muted">
                Breve mensaje del correo que le llegará a los usuarios que pueden realizar reformas. Opcional
            </small>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="pull-left">
                    <a href="javascript:history.go(-1)" class="btn btn-default tip" data-placement="top"
                       title="Regresar" style="border-radius: 20px;"><span aria-hidden="true"><i
                                    class="fa fa-arrow-left"></i> Regresar</span></a>
                </div>
                <div class="pull-right">
                    <a href="#" target="_blank">
                        <button class="btn btn-primary tip" id="guardar" data-placement="top" style="border-radius: 20px;"
                                type="submit" title="Guardar">Guardar
                            <span aria-hidden="true"><i class="fa fa-save"></i></span>
                        </button>
                    </a>
                </div>
            </div>

        </div>


        {!! Form::close() !!}


    </div>

@endsection

@section('scripts')
    <script>

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(document).ready(function () {
            $("#permit-reform-form").submit(function (e) {
                $("#guardar").prop("disabled", true);
            });
        });

    </script>
@endsection