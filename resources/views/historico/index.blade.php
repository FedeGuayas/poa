@extends('layouts.master')
@section('tile','Historico')
@section('breadcrumbs', Breadcrumbs::render('historico'))

@section('content')
    <div class="row">
        <div class="col-lg-6">
            @include('alert.alert_json')
            @include('alert.alert')
        </div>
    </div>


    {!! Form::open(['route'=>['admin.historico.index'],'method'=>'GET','class'=>'form_noEnter']) !!}
    <div class="row">
        <div class="col-lg-6">
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('ejercicio','',['class'=>'sr-only']) !!}
                    {!! Form::select('ejercicio',$list_ejercicios,$ejercicio,['class'=>'form-control selectpicker','placeholder'=>'Ejercicio','id'=>'ejercicio']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('mes','',['class'=>'sr-only']) !!}
                    {!! Form::select('mes',$list_meses,$mes,['class'=>'form-control selectpicker','placeholder'=>'Meses...','id'=>'mes']) !!}
                </div>
                {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Filtrar','id'=>'buscar', 'type'=>'submit','value'=>'buscar','name'=>'buscar']) !!}
                @if ( (Auth::user()->hasRole('administrador') || Auth::user()->hasRole('root') || Auth::user()->hasRole('responsable-poa') || Auth::user()->hasRole('analista')) )
                    {!! Form::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar',['class'=>'btn btn-success tip pull-right','data-placement'=>'top', 'title'=>'Exportar','id'=>'exportar', 'type'=>'submit','value'=>'exportar','name'=>'exportar']) !!}
                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    <hr>
    <div class="col-md-12">

        <div class="panel panel-success">
            <div class="panel-heading clearfix">Histórico - {{ $mes!="" ? $list_meses[$mes] : "Seleccione mes" }} - {{ $ejercicio!="" ? $list_ejercicios[$ejercicio] : "Seleccione ejercicio" }}
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">

                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="resumen_table"
                           cellspacing="0" style="display: none; font-size: 11px;">
                        <thead>
                        <tr>
                            <th style="width: 65px">Código</th>
                            <th>Act.</th>
                            <th>Item</th>
                            {{--<th style="width: 50px">Dirección</th>--}}
                            <th>POA-FDG</th>
                            <th>Extras</th>
                            <th>Dev_ESIGEF</th>
                            <th>Codif_ESIGEF</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                           {{-- <th class="search-filter">filtrar</th>--}}
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($historico as $res)
                            <tr>
                                <td>{{$res->cod_programa.'-'.$res->cod_actividad.'-'.$res->cod_item}}</td>
                                <td>{{$res->actividad}}</td>
                                <td>{{$res->item}}</td>
                                {{--<td>{{$res->area}}</td>--}}
                                <td>$ {{number_format($res->aiMonto,2,'.','')}}</td>{{--actual con reformas--}}
                                <td>$ {{number_format($res->ingresoExtra,2,'.','')}} </td>
                                <td>$ {{number_format(($res->devengado),2,'.','')}} </td>  {{--con los extras--}}
                                <td>$ {{number_format(($res->codificado),2,'.','')}} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}

    </div>{{--./col-md-12--}}

    <input type="hidden" name="_token" value="{{ csrf_token() }}">



@endsection


@section('scripts')

    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.which=== 13) {
                    return false;
                }
            });

            var table = $("#resumen_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                select: true,
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
                    },
                    "select": {
                        "rows": {
                            "_": "Ha seleccionado %d filas",
                            "0": "Click en una la fila para seleccionarla",
                            "1": "Solo 1 fila seleccionada"
                        }
                    }
                },
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                        i : 0;
                    };
                    // Total en todas las paginas
                    total_plan = api.column(3).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_ext = api.column(4).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_devengado = api.column(5).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_codificado = api.column(6).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);


                    // Total en la pagina actual
                    pageTotal_plan = api.column(3, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_ext = api.column(4, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_codif = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);


                    // actualzar total en el pie de tabla
                    $(api.column(3).footer()).html('$' + pageTotal_plan + '<p style="color: #0c199c">' + ' ( $' + total_plan + ' )' + '</p>');
                    $(api.column(4).footer()).html('$' + pageTotal_ext + '<p style="color: #0c199c">' + ' ( $' + total_ext + ' )' + '</p>');
                    $(api.column(5).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_devengado + ' )' + '</p>');
                    $(api.column(6).footer()).html('$' + pageTotal_codif + '<p style="color: #0c199c">' + ' ( $' + total_codificado + ' )' + '</p>');


                },
                lengthChange: true
//                dom: 'Blfrtip',
//                buttons: [
//                    {
//                        extend: 'excel',
//                        exportOptions: {
//                            columns: ':visible'
//                        }
//                    },
//                    'colvis'
//                ]
//                columnDefs: [ {
//                    targets: -1,
//                    visible: false
//                } ]

            });
//
//            table.buttons().container()
//                    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );

            table.buttons().container()
                    .appendTo('#resumen_table_wrapper .col-md-6:eq(0)');

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

            $("#ejercicio").on('change', function () {
                if ($(this).val() === '' || $(this).val() === 'placeholder') {
                    $('#exportar').prop('disabled', true);
                } else  $('#exportar').prop('disabled', false);

            });

        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });





    </script>

@endsection