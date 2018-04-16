@extends('layouts.plane')
@section('title','Inclusión PAC')

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
        {!! Form::open(['route'=>'store.inclusion-pac','method'=>'post','id'=>'form_inc_pac']) !!}
        {!! Form::hidden('pac_id',$pac->id) !!}
        {!! Form::hidden('cod_item',$pac->cod_item) !!}
        {!! Form::hidden('cpc',$pac->cpc) !!}
        {!! Form::hidden('tipo_compra',$pac->tipo_compra) !!}
        {!! Form::hidden('concepto',$pac->concepto) !!}
        <div class="row">
            <div class="page-header">
                <h1>Solicitud de Inclusión PAC
                </h1>
                @include('alert.alert')
                @include('alert.request')
            </div>

            <p class="text-right"> Guayaquil, {{$fecha_actual->day}} de {{$month}} del {{$fecha_actual->year}} </p>

            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">PAC INICIAL
                        <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#pac_inicial"
                           aria-expanded="false" aria-controls="pac_inicial"><i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <div class="panel-body collapse in" id="pac_inicial" style="overflow: auto;">

                        <table class="table table-bordered table-responsive" >
                            <thead>
                            <th>PARTIDA PRESUPUESTARIA / CUENTA CONTABLE</th>
                            <th>CÓDIGO / CATEGORÍA CPC</th>
                            <th>TIPO COMPRA</th>
                            <th>DETALLE DEL PRODUCTO</th>
                            <th>PAC INICIAL</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td width="150px;" style="vertical-align: middle">{{$pac->cod_item}}</td>
                                <td width="150px;" style="vertical-align: middle">{{$pac->cpc}}</td>
                                <td width="100px;" style="vertical-align: middle">{{$pac->tipo_compra}}</td>
                                <td>{{$pac->concepto}}</td>
                                <td width="200" style="vertical-align: middle">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                    <input type="number" name="presupuesto" step="0.01"
                                           class="form-control input-sm tip" data-placement="top" title="Valor sin incluir IVA"
                                           >
                                        </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="pull-left">
                    <a href="{{route('admin.pacs.index')}}" class="btn btn-default tip" data-placement="top" title="Regresar" style="border-radius: 20px;"><span aria-hidden="true"><i class="fa fa-arrow-left"></i> Regresar</span></a>
                </div>
                <div class="pull-right">
                    <a href="#" target="_blank">
                    <button class="btn btn-primary tip" id="guardar" data-placement="top" style="border-radius: 20px;"
                            type="submit" title="Guardar y Generar PDF">Guardar
                        <span aria-hidden="true"><i class="fa fa-file-pdf-o"></i></span>
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

        $(document).on('click', '.panel-heading .btn-collapse', function (e) {
            var $this = $(this);
            if (!$this.hasClass('panel-collapsed')) {
                $this.addClass('panel-collapsed');
                $this.find('i').removeClass('fa-minus').addClass('fa-plus');
            } else {
                $this.removeClass('panel-collapsed');
                $this.find('i').removeClass('fa-plus').addClass('fa-minus');
            }
        });

        $(document).ready(function () {
            $("#form_inc_pac").submit(function (e) {
                $("#guardar").prop("disabled", true);
            });
        });

    </script>
@endsection