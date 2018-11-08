@extends('layouts.master')
@section('title','PAC')

@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')
    @include('alert.alert')

    {!! Form::open(['route'=>'admin.pacs.store','method'=>'post','id'=>'form_pac']) !!}
    {!! Form::hidden('area_item_id',$area_item->id,['id'=>'area_item_id']) !!}
    {!! Form::hidden('mes',$area_item->cod,['id'=>'mes']) !!}
    {!! Form::hidden('cod_item',$codigos->cod_item,['id'=>'cod_item']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading clearfix">Procesos-{{$area->area}}
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
                        <div class="col-lg-2">DISPONIBLE:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('total_disponible',$total_disponible,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'total_disponible','readonly']) !!}
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
                                {{--{!! Form::label('procedimiento','Procedimiento') !!}--}}
                                {!! Form::select('procedimiento',['CATÁLOGO ELECTRÓNICO'=>'CATÁLOGO ELECTRÓNICO','CONTRATACIÓN DIRECTA'=>'CONTRATACIÓN DIRECTA','ÍNFIMA CUANTÍA'=>'ÍNFIMA CUANTÍA','LICITACIÓN DE SEGUROS'=>'LICITACIÓN DE SEGUROS','SUBASTA INVERSA ELECTRÓNICA'=>'SUBASTA INVERSA ELECTRÓNICA','OTRO'=>'OTRO'],null,['class'=>'form-control selectpicker','placeholder'=>'Procedimiento ...*','id'=>'procedimiento','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::select('tipo_compra',['BIEN'=>'BIEN','OBRA'=>'OBRA','SERVICIO'=>'SERVICIO','CONSULTORIA'=>'CONSULTORIA','OTRO'=>'OTRO'],null,['class'=>'form-control selectpicker','placeholder'=>'Tipo Compra ... *','id'=>'tipo_compra','required']) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="input-group has-warning small">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                    {!! Form::number('valor',null,['step' => '0.01','min' => '0','class'=>'form-control tip','data-placement'=>'top','title'=>'Valor','placeholder'=>'0.00','id'=>'valor','required']) !!}
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
                        <a href="{{route('indexPlanificacion')}}">
                            {!! Form::button('<i class="fa fa-undo" aria-hidden="true"></i> Regresar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Regresar']) !!}
                        </a>
                    </div>
                </div>{{--./panel-collapse--}}
            </div>{{--./panel-info--}}
        </div>{{--./col-md-12--}}

        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading clearfix">RESUMEN
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                       aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="resumen">

                    <table class="table table-striped table-bordered table-condensed table-hover table-responsive"
                           id="pac_table" cellspacing="0" width="100%" style="display: none;">
                        <thead class="bg-info">
                        <th>Cod_item</th>
                        <th>Presupuesto</th>
                        <th>Devengado</th>
                        <th>Disponible</th>
                        <th>Mes</th>
                        <th>Responsable</th>
                        <th>Procedimiento</th>
                        <th>Concepto</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Cod_item</th>
                            <th>Presupuesto</th>
                            <th>Devengado</th>
                            <th>Disponible</th>
                            <th>Mes</th>
                            <th>Responsable</th>
                            <th>Procedimiento</th>
                            <th>Concepto</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($pacs as $pac)
                            <tr>
                                <td>{{$pac->cod_item}}</td>
                                <td>{{$pac->presupuesto}}</td>
                                <td>{{$pac->devengado}}</td>
                                <td>{{$pac->disponible}}</td>
                                <td>{{$pac->mes}}</td>
                                <td>{{$pac->nombres}} {{$pac->apellidos}}</td>
                                <td>{{$pac->procedimiento}}</td>
                                <td>{{$pac->concepto}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>{{--./panel-success--}}
        </div>{{--./col-md-12--}}

    </div>{{--row--}}

    {!! Form::close() !!}

@endsection

@section('scripts')

    <script>

        $("form").submit('', function (event) {
            event.preventDefault();
            $("#guardar").prop("disabled", true);
            var form = $(this);
            var valor = parseFloat($("#valor").val());
            var disp = parseFloat($("#total_disponible").val());
            var resto = (disp - valor).toFixed(2);
            if (resto > 0) {
                event.preventDefault();
                swal({
                    title: "Guardar proceso?",
                    text: 'Existe disponibilidad de ($ ' + resto + '), seguro quiere guardar el proceso?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#6A9944",
                    confirmButtonText: "Si, guardar!",
                    cancelButtonText: "No, cancelar!",
                    closeOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        event.currentTarget.submit();//enviar el formulario invirtiendo el valor preventDefault
                    } else {
                        //acciones o funciones al dar en cancelar
                    }
                });
            } else if (valor > disp) {
                event.preventDefault();
                swal("Error!", "No puede superar el valor disponible!", "error")
            } else {
                event.currentTarget.submit();
            }

        });

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

        });
    </script>
@endsection