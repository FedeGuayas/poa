@extends('layouts.master')
@section('title','POA-FDG')

@section('breadcrumbs', Breadcrumbs::render('poa-fdg'))

@section('content')
    @include('alert.alert')

    {!! Form::model($pac,['route'=>['admin.pacs.update',$pac->id],'method'=>'PUT']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading clearfix">
                    EDITAR-PROCESO                    {{--{{$pac->area_item->area->area}}--}}
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa-area"
                       aria-expanded="false" aria-controls="poa-area"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="poa-area">

                    <div class="row">
                        <div class="col-lg-2">DISPONIBLE:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('total_disponible',$total_disponible,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'total_disponible','readonly']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">MES:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-calendar text-warning"></i></span>
                                {!! Form::text('month',$pac->month,['class'=>'form-control','id'=>'month','readonly']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">ITEM:
                            <div class="input-group has-success">
                                <span class="input-group-addon">{{$pac->cod_item}}</span>
                                <strong>
                                    {!! Form::text('item',$pac->item,['class'=>'form-control','id'=>'item','readonly','style'=>'text-align: center']) !!}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::select('worker_id',$list_workers,null,['class'=>'form-control selectpicker','placeholder'=>'Seleccione responsable...','id'=>'worker_id']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {{--{!! Form::label('procedimiento','Procedimiento') !!}--}}
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
                                    <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                    {!! Form::number('presupuesto',null,['step' => '0.01','min' => '0','class'=>'form-control tip','data-placement'=>'top','title'=>'Valor','placeholder'=>'0.00','id'=>'presupuesto']) !!}
                                    <span class="input-group-addon">.00</span>
                                </div>
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
                                {!! Form::textarea('concepto',null,['class'=>'form-control','placeholder'=>'Descripción del proceso...','rows'=>'3','style'=>'text-transform:uppercase']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::checkbox('proceso_pac',null,null,['id'=>'proceso_pac']) !!}
                                {!! Form::label('proceso_pac','Seleccione si es un proceso PAC') !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-primary tip guardar','data-placement'=>'top', 'title'=>'Guardar', 'type'=>'submit']) !!}
                        {!! Form::button('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'type'=>'reset', 'title'=>'Cancelar']) !!}
                        <a href="{{route('indexPlanificacion')}}">
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