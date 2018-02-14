@extends('layouts.master')
@section('title','Gestión')

@section('breadcrumbs', Breadcrumbs::render('pac-gestion'))

@section('content')
    <div class="row">
        <div class="col-sm-6">
            @include('alert.request')
            @include('alert.alert')
        </div>
    </div>


    {!! Form::open(['route'=>'admin.gestion.store','method'=>'post']) !!}
    {!! Form::hidden('pac_id',$pac->id,['id'=>'pac_id']) !!}

    <div class="row">
        <div class="col-md-2 col-sm-6">
            <h4>Disponible: <span class="label label-warning">$ {{$pac->disponible}}</span></h4>
        </div>
        <div class="col-md-2 col-sm-6">
            <a href="#permitReform" data-toggle="modal"
               class="btn btn-xs btn-danger tip" data-placement="top"
               title="Monto no utilizado, para reformar" target="_blank">
                <i class="fa fa-recycle" aria-hidden="true"></i> A Reformar
            </a>
            {{--<a href="{{route('pac.permitReform',$pac->id)}}">--}}
{{--                {!! Form::button('<i class="fa fa-recycle" aria-hidden="true"></i> A Reformar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'title'=>'Monto no utilizado, para reformar']) !!}--}}
            {{--</a>--}}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('num_doc','RUC:') !!} <span class="text-danger fa-lg">*</span>
                    {!! Form::text('num_doc',null,['class'=>'form-control','placeholder'=>'RUC del proveedor ...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('proveedor','Proveedor:') !!} <span class="text-danger fa-lg">*</span>
                    {!! Form::text('proveedor',null,['class'=>'form-control','placeholder'=>'Proveedor del proceso...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('num_factura','Número de Factura:') !!} <span class="text-danger fa-lg">*</span>
                    {!! Form::text('num_factura',null,['class'=>'form-control','placeholder'=>'Numero de la factura...','style'=>'text-transform:uppercase','required']) !!}
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="col-lg-3">
                <div class="form-group">
                    {!! Form::label('fecha_factura','Fecha de la Factura:') !!} <span class="text-danger fa-lg">*</span>
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
                    {!! Form::label('importe','Ejecutado:') !!} <span class="text-danger fa-lg">*</span>
                    <div class="input-group has-success">
                        <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                        {!! Form::number('importe',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'Devengado','placeholder'=>'0.00','id'=>'importe','step' => '0.01','min' => '0','required']) !!}
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
                {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-primary tip','data-placement'=>'top', 'title'=>'Guardar', 'type'=>'submit']) !!}
                {!! Form::button('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'type'=>'reset', 'title'=>'Cancelar']) !!}
                <a href="{{route('admin.pacs.index')}}">
                    {!! Form::button('<i class="fa fa-undo" aria-hidden="true"></i> Regresar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Regresar']) !!}
                </a>
            </div>

        </div>

    </div>


    {!! Form::close() !!}
@include('pac.permit-reform-modal')
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
            var table = $("#pac_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                "language": {
                    "decimal": "",
                    "emptyTable": "No se encontraron datos en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrados de un total _MAX_ registros)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encrontraron coincidencias",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Activar para ordenar ascendentemente",
                        "sortDescending": ": Activar para ordenar descendentemente"
                    }
                }
            });

            $("#pac_table").fadeIn();


            $('#pac_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="' + title + '" />');
            });

            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });


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

        $('#permitReform').on('show.bs.modal', function (event) {
//            var button = $(event.relatedTarget); // Button that triggered the modal
//            var de = button.data('user'); // Extract info from data-* attributes
//            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
//            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
//            var modal = $(this);
//            modal.find('.modal-title').text('NUevo men message to ' + de);
//            modal.find('.modal-body input').val(de);
        });

    </script>
@endsection