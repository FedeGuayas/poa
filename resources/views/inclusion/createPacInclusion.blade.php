@extends('layouts.master')
@section('title','PAC')

@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')
    @include('alert.alert')

    {!! Form::open(['route'=>'storePacInclusion','method'=>'post','id'=>'form_pac']) !!}
    {!! Form::hidden('area_item_id',$area_item->id,['id'=>'area_item_id']) !!}
    {!! Form::hidden('mes',$area_item->cod,['id'=>'mes']) !!}
    {!! Form::hidden('cod_item',$codigos->cod_item,['id'=>'cod_item']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading clearfix">INCLUSIÓN - PROCESOS -{{$area->area}}
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa-area"
                       aria-expanded="false" aria-controls="poa-area"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="poa-area">

                    <div class="row">
                        <div class="col-lg-2">CODIGO:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-hashtag text-info"></i></span>
                                <strong><input type="text" class="form-control input-sm" disabled
                                               value="{{$codigos->cod_programa}}-{{$codigos->cod_actividad}}-{{$codigos->cod_item}}"
                                               style="width: 100%; text-align: center"></strong>
                            </div>
                        </div>

                        <div class="col-lg-2">MES:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-calendar text-warning"></i></span>
                                {!! Form::text('month',$area_item->month,['class'=>'form-control','id'=>'month','readonly']) !!}
                            </div>
                        </div>

                        <div class="col-lg-6">ITEM:
                            <div class="input-group has-success">
                                <span class="input-group-addon">{{$codigos->cod_item}}</span>
                                <strong>
                                    {!! Form::text('item',$codigos->item,['class'=>'form-control','id'=>'item','readonly','style'=>'text-align: center']) !!}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::select('worker',$list_workers,null,['class'=>'form-control selectpicker','placeholder'=>'Seleccione responsable ... *','id'=>'worker','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::select('procedimiento',['CATÁLOGO ELECTRÓNICO'=>'CATÁLOGO ELECTRÓNICO','CONTRATACIÓN DIRECTA'=>'CONTRATACIÓN DIRECTA','ÍNFIMA CUANTÍA'=>'ÍNFIMA CUANTÍA','LICITACIÓN DE SEGUROS'=>'LICITACIÓN DE SEGUROS','SUBASTA INVERSA ELECTRÓNICA'=>'SUBASTA INVERSA ELECTRÓNICA','OTRO'=>'OTRO'],null,['class'=>'form-control selectpicker','placeholder'=>'Procedimiento ...*','id'=>'procedimiento','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::select('tipo_compra',['BIEN'=>'BIEN','OBRA'=>'OBRA','SERVICIO'=>'SERVICIO','CONSULTORIA'=>'CONSULTORIA'],null,['class'=>'form-control selectpicker','placeholder'=>'Tipo Compra ... *','id'=>'tipo_compra','required']) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="input-group has-warning small">
                                    <span class="input-group-addon"><i class="fa fa-hashtag text-warning"></i></span>
                                    {!! Form::number('cpc',null,['step' => '1','min' => '0','class'=>'form-control tip','data-placement'=>'top','title'=>'CPC','placeholder'=>'CPC','id'=>'cpc']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::label('concepto','Concepto:*') !!}
                                {!! Form::textarea('concepto',null,['class'=>'form-control','placeholder'=>'Descripción del proceso...','rows'=>'3','style'=>'text-transform:uppercase','id'=>'concepto','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::checkbox('proceso_pac',null,true,['id'=>'proceso_pac']) !!}
                                {!! Form::label('proceso_pac','Seleccione si es un proceso PAC') !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-primary tip','data-placement'=>'top', 'title'=>'Guardar','type'=>'submit','id'=>'guardar']) !!}
                        {!! Form::button('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'type'=>'reset', 'title'=>'Cancelar']) !!}
                        <a href="{{route('indexIncPac')}}">
                            {!! Form::button('<i class="fa fa-undo" aria-hidden="true"></i> Regresar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Regresar']) !!}
                        </a>
                    </div>
                </div>{{--./panel-collapse--}}
            </div>{{--./panel-info--}}
        </div>{{--./col-md-12--}}

    </div>{{--row--}}

    {!! Form::close() !!}

@endsection

@section('scripts')

    <script>

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(".selectpicker").selectpicker({
            liveSearch: true,
            liveSearchPlaceholder: 'Buscar'
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

    </script>
@endsection