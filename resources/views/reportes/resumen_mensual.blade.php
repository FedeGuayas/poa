@extends('layouts.master')
@section('tile','Resumen Mensual')
@section('breadcrumbs', Breadcrumbs::render('reporte-mensual'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')
    {!! Form::open(['route'=>['admin.reportes.resumen_mensual'],'method'=>'GET','class'=>'form_noEnter']) !!}
    <div class="row">
        <div class="col-lg-2">
            <div class="form-group">
                {!! Form::label('mes','',['class'=>'sr-only']) !!}
                {!! Form::select('mes',$list_meses,$mes,['class'=>'form-control selectpicker','placeholder'=>'Meses...','id'=>'mes']) !!}
            </div>
        </div>
        <div class="col-lg-1">
            {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip pull-right','data-placement'=>'top', 'title'=>'Filtrar','id'=>'buscar', 'type'=>'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    <hr>
    <div class="col-md-12">

        <div class="panel panel-success">
            <div class="panel-heading clearfix">Resumen - {{$list_meses[$mes]}}. Planificado FDG VS Devengado ESIGEF
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="resumen_table"
                           cellspacing="0" style="display: none;">
                        {{--<caption>--}}
                            {{--@if ($mes)--}}
                            {{--<a href="#!" class="btn btn-xs btn-default tip" data-placement="top" title="Imprimir" target="_blank">--}}
                                {{--<i class="fa fa-2x fa-print"></i>--}}
                            {{--</a>--}}
                            {{--@endif--}}
                        {{--</caption>--}}
                        <thead>
                            <tr>
                                <th>Dirección</th>
                                <th>Planificado</th>
                                <th>Devengado</th>{{--Devengado esigef - Ingresos extras--}}
                                <th>No Ejecutado</th>{{--Planificado FDG - Devengado esigef - Ingresos extras--}}
                                <th>%Ejec</th>
                                <th>%No Ejec</th>
                                {{--<th>extra</th>--}}
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Total General</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                {{--<th></th>--}}
                            </tr>
                        </tfoot>
                        <tbody>
                        @foreach($resumen as $res)
                            <tr>
                                <td>{{$res->area}}</td>
                                <td>$ {{number_format($res->planificado,2,'.',' ')}}</td>
                                <td>
                                $ {{number_format(($res->devengado-$res->extra),2,'.',' ')}}
                                </td>
                                <td>
                                    $ {{number_format(($res->planificado-($res->devengado-$res->extra)),2,'.',' ')}}
                                </td>
                                <td>
                                    @if ($res->devengado/$res->total*100 <=60)
                                        <span class="label la-2x label-danger">{{number_format($res->devengado/$res->total*100,2,'.',' ')}} %</span>
                                        @elseif($res->devengado/$res->total*100 <=90 )
                                        <span class="label la-2x label-warning">{{number_format($res->devengado/$res->total*100,2,'.',' ')}} %</span>
                                        @else
                                        <span class="label la-2x label-success">{{number_format($res->devengado/$res->total*100,2,'.',' ')}} %</span>
                                    @endif
                                </td>
                                <td>
                                    {{number_format(100-($res->devengado/$res->total*100),2,'.','')}} %
                                </td>
                                {{--<td>{{$res->extra}}</td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}


        <div class="panel panel-info">
            <div class="panel-heading clearfix">Info del PAC
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen2"
                   aria-expanded="false" aria-controls="resumen2"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen2">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="resumen2_table"
                           cellspacing="0" style="display: none;">
                        <thead>
                            <th style="width: 80px">Dirección</th>
                            <th style="width: 100px">Código</th>
                            <th>Item</th>
                            <th>Ejecutado</th>
                            <th>Devengado</th>
                            <th>Disponible</th>
                            <th>Responsable</th>
                            <th>Procedimiento</th>
                            <th>Concepto</th>
                        </thead>
                        <tfoot>
                        <th class="search-filter">filtrar</th>
                        <th class="search-filter">filtrar</th>
                        <th class="search-filter">filtrar</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="search-filter">filtrar</th>
                        <th class="search-filter">filtrar</th>
                        <th class="search-filter">filtrar</th>
                        </tfoot>
                        <tbody>
                        @foreach($devengado_pacs as $pacs)
                            <tr>
                                <td>{{$pacs->area}}</td>
                                <td>{{$pacs->cod_programa.'-'.$pacs->cod_actividad.'-'.$pacs->cod_item}}</td>
                                <td>{{$pacs->item}}</td>
                                <td>$ {{number_format($pacs->comprometido,2,'.','')}}</td>
                                <td>$ {{number_format($pacs->devengado,2,'.','')}}</td>
                                <td>$ {{number_format($pacs->disponible,2,'.','')}}</td>
                                <td>{{$pacs->nombres}} {{$pacs->apellidos}}</td>
                                <td>{{$pacs->procedimiento}}</td>
                                <td>{{$pacs->concepto}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        {{--<tr>--}}
                        {{--<th>TOTAL</th>--}}
                        {{--<th>$ {{number_format($total_p,2,'.',' ')}}</th>--}}
                        {{--<th>$ {{number_format($total_e,2,'.',' ')}}</th>--}}
                        {{--<th>$ {{number_format($total_esigef,2,'.',' ')}}</th>--}}
                        {{--<th>--}}
                        {{--@if (($total_p+$total_e)>0)--}}
                        {{--{{number_format(($total_esigef/$total_p+$total_e)*100,2,'.',' ')}}%--}}
                        {{--@endif--}}
                        {{--</th>--}}
                        {{--</tr>--}}
                    </table>
                </div>
            </div>
        </div>{{--./panel-info--}}


    </div>{{--./col-md-12--}}
<div id="prueba">


</div>



@endsection


@section('scripts')
    <script src="{{asset('js/renderSection.js')}}"></script>
    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.width == 13) {
                    return false;
                }
            });


            var table = $("#resumen_table").DataTable({
                lengthMenu: [[10,15, -1], [10,15, 'Todo']],
                select:true,
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
                        "colvis": "Cambiar Columnas",
                        "copy": "Copiar",
                        "print": "Imprimir"
                    },
                    "select": {
                        "rows": {
                            "_": "Ha seleccionado %d filas",
                            "0": "Click en una la fila para seleccionarla",
                            "1": "Solo 1 fila seleccionada"
                        }
                    }
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function ( i ) {
//                        return typeof i === 'string' ?
//                        i.replace(/[\$,]/g, '')*1 :
//                                typeof i === 'number' ?
//                                        i : 0;
                        return typeof i === 'string' ?
                        i.replace(/[^\d.-]/g, '')*1 :
                                typeof i === 'number' ?
                                        i : 0 ;
                    };
                    // Total en la pagina actual
                    pageTotal_plan = api.column(1,{ page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    pageTotal_dev = api.column(2,{ page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    pageTotal_disp = api.column( 3, { page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b));
                    }, 0 );
                    pageTotal_porciento_eje = api.column( 4, { page: 'current'} ).data().reduce( function (a, b) {
                        return (pageTotal_dev/pageTotal_plan*100).toFixed(2);
                    }, 0 );
                    pageTotal_porciento_noeje = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) {
                        return (pageTotal_disp/pageTotal_plan*100).toFixed(2);
                    }, 0 );

                    // actualzar total en el pie de tabla
                    $( api.column( 1 ).footer() ).html('$'+pageTotal_plan );
                    $( api.column( 2 ).footer() ).html('$'+pageTotal_dev );
                    $( api.column( 3 ).footer() ).html('$'+pageTotal_disp );
                    $( api.column( 4 ).footer() ).html( pageTotal_porciento_eje+' %');
                    $( api.column( 5 ).footer() ).html( pageTotal_porciento_noeje+' %' );
                },
                lengthChange: true,
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ]
//                columnDefs: [ {
//                    targets: -1,
//                    visible: false
//                } ]

            });
//
//            table.buttons().container()
//                    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );

            table.buttons().container()
                    .appendTo( '#resumen_table_wrapper .col-md-6:eq(0)' );



            $("#resumen_table").fadeIn();


            $('#resumen_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
            });

            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });


            var table2 = $("#resumen2_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                select:true,
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
                    "select": {
                        "rows": {
                            "_": "Ha seleccionado %d filas",
                            "0": "Click en una la fila para seleccionarla",
                            "1": "Solo 1 fila seleccionada"
                        }
                    }
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                        i : 0;
                    };
                    // Total en todas las paginas
                    total_ejecutado = api.column( 3 ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    total_devengado = api.column( 4 ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    total_disponible = api.column( 5 ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );


                    // Total en la pagina actual
                    pageTotal_eje = api.column(3,{ page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    pageTotal_dev = api.column(4,{ page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );
                    pageTotal_disp = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                    // actualzar total en el pie de tabla
                    $( api.column( 3 ).footer() ).html('$'+pageTotal_eje +'<p style="color: #0c199c">'+' ( $'+ total_ejecutado +' )'+'</p>');
                    $( api.column( 4 ).footer() ).html('$'+pageTotal_dev +'<p style="color: #0c199c">'+' ( $'+ total_devengado +' )'+'</p>');
                    $( api.column( 5 ).footer() ).html('$'+pageTotal_disp +'<p style="color: #0c199c">'+' ( $'+ total_disponible +' )'+'</p>');
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'PAC - AREA',
                        message: 'Presupuesto anual de compras ',
                        orientation: 'landscape',
                        pageSize: 'letter',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ],
                columnDefs: [ {
//                    targets: -1,
                    visible: false
                } ]
            });

            $("#resumen2_table").fadeIn();


            $('#resumen2_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
            });

            table2.columns().every(function () {
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


    </script>

@endsection