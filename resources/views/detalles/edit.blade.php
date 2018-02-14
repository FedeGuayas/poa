@extends('layouts.master')
@section('title','Gestión')

@section('breadcrumbs', Breadcrumbs::render('pac-gestion'))

@section('content')
    @include('alert.alert')

    {!! Form::model($gestion,['route'=>['admin.gestion.update',$gestion->id],'method'=>'PUT']) !!}

    <div class="row">
        <div class="col-lg-10">
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('num_doc','RUC:') !!}
                    {!! Form::text('num_doc',null,['class'=>'form-control','placeholder'=>'RUC del proveedor ...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('proveedor','Proveedor:') !!}
                    {!! Form::text('proveedor',null,['class'=>'form-control','placeholder'=>'Proveedor del proceso...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('num_factura','Número de Factura:') !!}
                    {!! Form::text('num_factura',null,['class'=>'form-control','placeholder'=>'Numero de la factura...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('fecha_factura','Fecha de la Factura:') !!}
                    <div class='input-group' id="fecha_factura_datepicker">
                        {!! Form::text('fecha_factura',null,['class'=>'form-control','style'=>'text-transform:uppercase','onkeydown'=>'return false','required']) !!}
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('fecha_entrega','Fecha de entrega:') !!}
                    <div class='input-group' id="fecha_entrega_datepicker">
                        {!! Form::text('fecha_entrega',null,['class'=>'form-control','style'=>'text-transform:uppercase','onkeydown'=>'return false']) !!}
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('importe','Ejecutado:') !!}
                    <div class="input-group has-success">
                        <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                        {!! Form::number('importe',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'Devengado','placeholder'=>'0.00','id'=>'importe','step' => '0.01','min' => '0','required','readonly']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="col-lg-6">
                <div class="form-group">
                    <i class="fa fa-pencil"></i>
                    {!! Form::label('nota','Observaciones:') !!}
                    {!! Form::textarea('nota',null,['class'=>'form-control','length'=>'255','style'=>'text-transform:uppercase','placeholder'=>'Observaciones...','rows'=>'3', 'cols'=>'50']) !!}
                </div>
            </div>
            <div class="col-lg-6 form-group">
                {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar',['class'=>'btn btn-sm btn-primary tip','data-placement'=>'top', 'title'=>'Guardar', 'type'=>'submit']) !!}
                {!! Form::button('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'type'=>'reset', 'title'=>'Cancelar']) !!}
                <a href="javascript:history.back()">
                    {!! Form::button('<i class="fa fa-undo" aria-hidden="true"></i> Regresar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Regresar']) !!}
                </a>
            </div>

        </div>

    </div>

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

        $(document).ready(function () {
            $('#fecha_factura_datepicker').datetimepicker({
//                daysOfWeekDisabled: [0, 6],
                showClear:true,
                showClose:true,
                allowInputToggle:true,
//                minDate: moment(),
//                disabledTimeIntervals:[],
//                enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                locale:'es',
                format:'YYYY-MM-DD'
            });
            $('#fecha_entrega_datepicker').datetimepicker({
//                daysOfWeekDisabled: [0, 6],
                showClear:true,
                showClose:true,
                useCurrent: false, //Important! See issue #1075
                allowInputToggle:true,
//                enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                locale:'es',
                format:'YYYY-MM-DD'

            });
            $("#fecha_factura_datepicker").on("dp.change", function (e) {
                $('#fecha_entrega_datepicker').data("DateTimePicker").minDate(e.date);
            });
            $("#fecha_entrega_datepicker").on("dp.change", function (e) {
                $('#fecha_factura_datepicker').data("DateTimePicker").maxDate(e.date);
            });

        });
    </script>
@endsection