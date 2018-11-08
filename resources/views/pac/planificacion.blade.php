@extends('layouts.master')
@section('title','PAC-Plan')
@section('breadcrumbs', Breadcrumbs::render('pac-plan'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')

    <div class="col-md-12">
        {!! Form::open(['route'=>['indexPlanificacion'],'method'=>'GET','class'=>'form_noEnter', 'id'=>'form_item_store']) !!}
        <div class="form-inline">
            <div class="form-group">
                {{--{!! Form::label('area','Areas') !!}--}}
                {!! Form::select('area',$list_areas,$area_select,['class'=>'form-control','placeholder'=>'Direcciones...','id'=>'area']) !!}
            </div>
            {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Buscar','id'=>'buscar', 'type'=>'submit']) !!}
        </div>
        {!! Form::close() !!}
        <hr>

        <div class="panel panel-info">
            <div class="panel-heading clearfix">Planificación anual - FDG
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa-area"
                   aria-expanded="false" aria-controls="poa-area"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="poa-area">
                <div class="col-lg-12">
                    {!! Form::open(['route'=>'pacs.generateAutomaticProcess','method'=>'post','id'=>'form_proc_automatic']) !!}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" id="ai_table"
                               cellspacing="0" width="100%" style="display: none;">
                            <caption>POA-Direcciones</caption>
                            <thead class="bg-info">
                            <th style="width: 100px;">Cod_item</th>
                            <th>Item</th>
                            <th style="width: 150px;">Dirección</th>
                            <th style="width: 150px;">Plan</th>
                            <th style="width: 150px;">Disp.</th>
                            <th style="width: 100px;">Mes</th>
                            <th>Proc.</th>
                            <th style="width: 45px;">
                                {{--Check de seleccionar todos los check para generar procesos automaticamente--}}
                                {!! Form::checkbox('select_all_items',null,false,['id'=>'select_all_items','class'=>'checkbox tip','data-placement'=>'top', 'title'=>'Seleccionar todo']) !!}
                                {{--Crear Procesos Automaticamente con los item seleccionados y sus valores disponibles--}}
                                @permission('planifica-pac')
                                {!! Form::button('<i class="fa fa-check-square-o" aria-hidden="true"></i>',['class'=>'btn-xs btn-info tip','data-placement'=>'top', 'title'=>'Procesos Auto.','id'=>'proceso_automatico']) !!}
                                @endpermission
                            </th>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                                <th></th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                               <th></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach($area_item as $ai)
                                <tr>
                                    <td>{{$ai->cod_programa.'-'.$ai->cod_actividad.'-'.$ai->cod_item}}</td>
                                    <td>{{$ai->item}}</td>
                                    <td>{{$ai->area}}</td>
                                    <td>${{$ai->monto}}</td>
                                    <td>${{($ai->monto-$ai->distribuido)}}</td>
                                    <td>{{$ai->mes}}</td>
                                    {{--Si el usuario pertenece al area a la que se repartio el dinero o es root y Si hay disponibilidad de ese poa --}}
                                    @if ( (Auth::user()->worker->departamento->area->area==$ai->area || Auth::user()->hasRole('root')) && ($ai->monto-$ai->distribuido)>0)
                                    @permission('planifica-pac')
                                    <td>
                                        {{--Si no es una inclusion--}}
                                            @if ($ai->inclusion==\App\AreaItem::INCLUSION_NO)
                                                <a href="{{route('createPac',$ai->id)}}"
                                                   class="btn btn-xs btn-success tip"
                                                   data-placement="top" title="Crear Proceso">
                                                    <i class="fa fa-money"></i>
                                                </a>
                                            @endif
                                    </td>
                                    <td align="center">

                                        <a href="#">
                                            {!! Form::checkbox('gen_proc[]',$ai->id,false,['id'=>'ai'.$ai->id,'class'=>'tip','data-placement'=>'top', 'title'=>'Generar']) !!}
                                        </a>

                                    </td>
                                    @endpermission
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>{{--./panel-collapse--}}
        </div>{{--./panel-info--}}
    </div>{{--./col-md-12--}}

    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading clearfix">Procesos - {{count($area)>0 ? $area->area : ""}}
                <a href="#" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">

                <table class="table table-striped table-bordered table-condensed table-hover table-responsive"
                       id="pac_table" cellspacing="0" width="100%" style="display: none;">
                    <thead class="bg-info">
                    <th>Cod_item</th>
                    <th>Presupuesto</th>
                    <th>Ejecutado</th>
                    <th>Devengado</th>
                    <th>Disponible</th>
                    <th>Procedimiento</th>
                    <th>Concepto</th>
                    <th>Mes</th>
                    <th>Responsable</th>
                    <th>Acción</th>
                    </thead>
                    <tfoot>
                    <tr>
                        <th class="search-filter">filtrar</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="search-filter">filtrar</th>
                        <th class="search-filter">filtrar</th>
                        <th></th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($pacs as $pac)
                        <tr>
                            <td>{{$pac->cod_programa.'-'.$pac->cod_actividad.'-'.$pac->cod_item}}</td>
                            <td>{{$pac->presupuesto}}</td>
                            <td>{{$pac->comprometido}}</td>
                            <td>{{$pac->devengado}}</td>
                            <td>{{$pac->disponible}}</td>
                            <td>{{$pac->procedimiento}}</td>
                            <td>{{$pac->concepto}}</td>
                            <td>{{$pac->mes}}</td>
                            <td>{{$pac->nombres}} {{$pac->apellidos}}</td>
                            <td>
                                <a href="{{route('admin.pacs.edit',$pac->id)}}"
                                   class="btn btn-xs btn-success tip"
                                   data-placement="top" title="Editar"> <i class="fa fa-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-xs btn-danger delete tip" data-placement="top"
                                   title="Eliminar"
                                   data-id="{{$pac->id}}"><i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

    {!! Form::open(['route'=>['admin.pacs.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
    {!! Form::close() !!}

@endsection


@section('scripts')
    <script src="{{asset('js/renderSection.js')}}"></script>
    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.which === 13) {
                    return false;
                }
            });

            var table = $("#ai_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                stateSave: true,
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
                },
                "columnDefs": [
                    { "orderable": false, "targets": [6,7] }
                ],
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    // Total en todas las paginas
                    total_plan = api.column(3).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_disp = api.column(4).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // Total en la pagina actual
                    pageTotal_plan = api.column(3, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    pageTotal_disp = api.column(4, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(3).footer()).html('$' + pageTotal_plan + '<p style="color: #0c199c">' + ' ( $' + total_plan + ' )' + '</p>');
                    $(api.column(4).footer()).html('$' + pageTotal_disp + '<p style="color: #0c199c">' + ' ( $' + total_disp + ' )' + '</p>');
                }
            });

            var table_pac = $("#pac_table").DataTable({
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
                    },
                    "buttons": {
                        "colvis": "Columnas",
                        "copy": "Copiar",
                        "print": "Imprimir"
                    }
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'POA - AREA',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'POA - AREA',
                        message: 'Presupuesto asignado',
                        orientation: 'landscape',
                        pageSize: 'letter',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ],
                columnDefs: [{
//                    targets: -1,
                    visible: false
                }],
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    // Total en todas las paginas
                    total_pre = api.column(1).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_ejec = api.column(2).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_dev = api.column(3).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_disp = api.column(4).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // Total en la pagina actual
                    pageTotal_pre = api.column(1, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_ejec = api.column(2, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(3, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_disp = api.column(4, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(1).footer()).html('$' + pageTotal_pre + '<p style="color: #0c199c">' + ' ( $' + total_pre + ' )' + '</p>');
                    $(api.column(2).footer()).html('$' + pageTotal_ejec + '<p style="color: #0c199c">' + ' ( $' + total_ejec + ' )' + '</p>');
                    $(api.column(3).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_dev + ' )' + '</p>');
                    $(api.column(4).footer()).html('$' + pageTotal_disp + '<p style="color: #0c199c">' + ' ( $' + total_disp + ' )' + '</p>');
                }
            });

            $("#pac_table").fadeIn();
            $("#ai_table").fadeIn();


            $('#pac_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" class="input-sm" style="width: 80%" placeholder="' + title + '" />');
            });

            $('#ai_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" class="input-sm" style="width: 80%;" placeholder="' + title + '" />');
            });

            table_pac.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
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

        //seleccionar todos los check para informe
        $(document).on('change', '#select_all_items', function (event) {
            $("input[name='gen_proc[]']").prop('checked', $(this).prop("checked"));
        });

        $(document).on('click', '#proceso_automatico', function (e) {
            e.preventDefault();
            var form = $("#form_proc_automatic")
            var url = form.attr('action');
            var data = form.serialize();
            swal({
                    title: "Confirme para continuar!",
                    text: "Se generará un proceso automaticamente con el monto disponible por cada linea seleccionada!",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "SI!",
                    cancelButtonText: " NO!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm) {
                        form.submit();
                    }//isConfirm
                    else {
                        swal("Cancelado", "Canceló la acción :)", "error");
                    }
                });

        });




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

        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var row = $(this).parents('tr');
            var id = $(this).attr('data-id');
            var form = $("#form-delete")
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                    title: "Confirme para eliminar !",
                    text: "Se eliminará el PROCESO. Esta acción no se podrá deshacer!",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "SI!",
                    cancelButtonText: " NO!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true,
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: url,
                            data: data,
                            type: 'POST',
                            success: function (response) {
                                if (response.tipo==='error'){
                                    swal("", response.message, "error");
                                }else {
                                    swal("Confirmado!", response.message, "success");
                                    row.fadeOut();
                                }
                            },
                            error: function (response) {
                                row.show();
                                swal("ERROR!", response, "error");
                            }
                        });
                    }//isConfirm
                    else {
                        swal("Cancelado", "Canceló la acción :)", "error");
                    }
                });

        });

    </script>

@endsection