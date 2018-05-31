@extends('layouts.master')
@section('title','PAC-Inclusión')
@section('breadcrumbs', Breadcrumbs::render('pac-plan'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')

    <div class="col-md-12">

        {!! Form::open(['route'=>['indexIncPac'],'method'=>'GET','class'=>'form_noEnter', 'id'=>'form_item_store']) !!}
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
            <div class="panel-heading clearfix">INCLUSION - PROCESOS
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa-area"
                   aria-expanded="false" aria-controls="poa-area"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="poa-area">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" id="ai_table"
                               cellspacing="0" width="100%" style="display: none;">
                            <caption>POA-Direcciones</caption>
                            <thead class="bg-info">
                            <th style="width: 100px;">Cod_item</th>
                            <th>Item</th>
                            <th style="width: 150px;">Dirección</th>
                            <th style="width: 100px;">Mes</th>
                            <th style="width: 100px;">PAC</th>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach($area_item as $ai)
                                <tr>
                                    <td>{{$ai->cod_programa.'-'.$ai->cod_actividad.'-'.$ai->cod_item}}</td>
                                    <td>{{$ai->item}}</td>
                                    <td>{{$ai->area}}</td>
                                    <td>{{$ai->mes}}</td>
                                    <td>
                                        @permission('planifica-pac')
                                        {{--Si hay disponibilidad de ese poa o es una inclusion, en cuyo caso el disponible=0--}}
                                        {{--Si el usuario pertenece al area a la que se repartio el dinero o es root --}}
                                        @if (Auth::user()->worker->departamento->area->area==$ai->area || Auth::user()->hasRole('root'))
                                            {{--Si es una inclusion poa--}}
                                            @if ($ai->inclusion==\App\AreaItem::INCLUSION_YES)
                                                <a href="{{route('createPacInclusion',$ai->id)}}"
                                                   class="btn btn-xs btn-danger tip"
                                                   data-placement="top" title="Inclusión Item Nuevo">
                                                    <i class="fa fa fa-money"></i>
                                                </a>
                                            @else
                                                <a href="{{route('createPacInclusion',$ai->id)}}"
                                                   class="btn btn-xs btn-success tip"
                                                   data-placement="top" title="Inclusión Item existente">
                                                    <i class="fa fa fa-money"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{--./panel-collapse--}}
        </div>{{--./panel-info--}}
    </div>{{--./col-md-12--}}

    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading clearfix">Inclusiones - {{count($area)>0 ? $area->area : ""}}
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">

                <table class="table table-striped table-bordered table-condensed table-hover table-responsive"
                       id="pac_table" cellspacing="0" width="100%" style="display: none;">
                    <thead class="bg-info">
                    <tr>
                        <th>Cod_item</th>
                        <th>Procedimiento</th>
                        <th>Tipo_compra</th>
                        <th>Concepto</th>
                        <th>Mes</th>
                        <th>Responsable</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th class="tfoot_search">Cod</th>
                        <th class="tfoot_select"></th>
                        <th class="tfoot_select"></th>
                        <th class="tfoot_search">Concepto</th>
                        <th class="tfoot_search">Mes</th>
                        <th class="tfoot_search">filtrar</th>
                        <th></th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($inclusiones as $inc)
                        <tr>
                            <td>{{$inc->cod_programa.'-'.$inc->cod_actividad.'-'.$inc->cod_item}}</td>
                            <td>{{$inc->procedimiento}}</td>
                            <td>{{$inc->tipo_compra}}</td>
                            <td>{{$inc->concepto}}</td>
                            <td>{{$inc->mes}}</td>
                            <td>{{$inc->nombres}} {{$inc->apellidos}}</td>
                            <td>

                                @permission('planifica-pac')
                                {{--Si el usuario pertenece al area a la que se repartio el dinero--}}
                                @if (Auth::user()->worker->departamento->area->area==$inc->area || Auth::user()->hasRole('root'))
                                    {{--Si no se han realizado movimientos de dinero en el pac--}}
                                    @if ($inc->presupuesto == 0 )
                                        <a href="{{route('editIncPac',$inc->id)}}"
                                        class="btn btn-xs btn-success tip"
                                        data-placement="top" title="Editar"> <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#!" class="btn btn-xs btn-danger delete tip" data-placement="top"
                                        title="Eliminar"
                                        data-id="{{$inc->id}}"><i class="fa fa-trash-o"></i>
                                        </a>
                                    @endif
                                @endif
                                @endpermission
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

    {!! Form::open(['route'=>['destroyInclusionPac',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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
                }

            });

            //texto de input para filtrar
            $('.tfoot_search').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
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
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        //input text
                        if ($(column.footer()).hasClass('tfoot_search')) {
                            //aplicar la busquedad
                            var that = this;
                            $('input', this.footer()).on('keyup change', function () {
                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                }
                            });

                        }
                        else if ($(column.footer()).hasClass('tfoot_select')) { //select
                            var column = this;
                            //aplicar la busquedad
                            var select = $('<select style="width: 100%"><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                });

                            column.data().unique().sort().each(function (d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>')
                            });
                        }
                    });
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
                    text: "Se eliminará la INCLUSION. Esta acción no se podrá deshacer!",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "SI!",
                    cancelButtonText: " NO!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
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