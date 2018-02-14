@extends('layouts.master')
@section('title','Cierre Mensual')
@section('breadcrumbs', Breadcrumbs::render('cierre'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')

    {!! Form::open(['route'=>['admin.historico.cierre'],'method'=>'GET','class'=>'form_noEnter', 'id'=>'form_cierre']) !!}
    <div class="row">
        <div class="col-lg-6">
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('mes','',['class'=>'sr-only']) !!}
                    {!! Form::select('mes',$list_meses,$mes,['class'=>'form-control selectpicker','placeholder'=>'Meses...','id'=>'mes']) !!}
                </div>
                {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Filtrar','id'=>'buscar', 'type'=>'submit']) !!}

            </div>
        </div>
    </div>

    <hr>
    <div class="col-md-12">

        <div class="panel panel-success">
            <div class="panel-heading clearfix">Cierre - {{$list_meses[$mes]}}.
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="table-responsive">
                    @permission('hacer-cierre')
                    <button type="button" class="btn btn-danger tip pull-right" data-placement="top"
                            title="Hacer Cierre" id="cierre"><i class="fa fa-clone" aria-hidden="true"></i> Cierre
                    </button>
                    @endpermission
                    <table class="table table-striped table-bordered table-condensed table-hover" id="resumen_table"
                           cellspacing="0" style="display: none; font-size: 11px;">
                        {{--<caption>--}}
                        {{--@if ($mes)--}}
                        {{--<a href="#!" class="btn btn-xs btn-default tip" data-placement="top" title="Imprimir" target="_blank">--}}
                        {{--<i class="fa fa-2x fa-print"></i>--}}
                        {{--</a>--}}
                        {{--@endif--}}
                        {{--</caption>--}}
                        <thead>
                        <tr>
                            <th>Ejer.</th>
                            <th style="width: 65px">Código</th>
                            <th>Act.</th>
                            <th>Item</th>
                            <th style="width: 50px">Dirección</th>

                            <th>Plan</th>
                            <th>Extras</th>
                            <th>Dev_ESIGEF</th>
                            <th>Codif_ESIGEF</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($cierre_mensual as $res)
                            <tr>
                                <td>{{$res->ejercicio}}</td>
                                <td>{{$res->cod_programa.'-'.$res->cod_actividad.'-'.$res->cod_item}}</td>
                                <td>{{$res->actividad}}</td>
                                <td>{{$res->item}}</td>
                                <td>{{$res->area}}</td>
                                <td>$ {{number_format($res->planificado,2,'.','')}}</td>
                                <td>$ {{number_format($res->extra,2,'.','')}} </td>
                                <td>$ {{number_format(($res->dev_esigef),2,'.','')}} </td>
                                <td>$ {{number_format(($res->cod_esigef),2,'.','')}} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}

    </div>{{--./col-md-12--}}

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    {!! Form::close() !!}


@endsection


@section('scripts')

    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.width == 13) {
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
                    total_plan = api.column(5).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_ext = api.column(6).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_devengado = api.column(7).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_codificado = api.column(8).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // Total en la pagina actual
                    pageTotal_plan = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_ext = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_cod = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(5).footer()).html('$' + pageTotal_plan + '<p style="color: #0c199c">' + ' ( $' + total_plan + ' )' + '</p>');
                    $(api.column(6).footer()).html('$' + pageTotal_ext + '<p style="color: #0c199c">' + ' ( $' + total_ext + ' )' + '</p>');
                    $(api.column(7).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_devengado + ' )' + '</p>');
                    $(api.column(8).footer()).html('$' + pageTotal_cod + '<p style="color: #0c199c">' + ' ( $' + total_codificado + ' )' + '</p>');

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

            $("#mes").on('change', function () {
                if ($(this).val() == '' || $(this).val() == 'placeholder') {
                    $('#cierre').prop('disabled', true);
                } else  $('#cierre').prop('disabled', false);

            });

            $("#cierre").on('click', function () {
                cierre();
            });


        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        function cierre() {
            var token = $("input[name=_token]").val();
            var route = "{{route('admin.historico.store')}}";
            var mes = $("#mes").val();
            var data = {
                mes: mes
            };
            if (mes != '' ){
                swal({
                    title: "",
                    text: "Se realizará el cierre del mes seleccionado!, seguro desea continuar?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "SI!",
                    cancelButtonText: " NO!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: route,
                            type: "POST",
                            headers: {'X-CSRF-TOKEN': token},
                            dataType: 'json',
                            data: data,
                            success: function (response) {
                                if (response.tipo == "error") {
                                    swal("Error", response.response, "error");
                                } else swal("", response.response, "success");
                            },
                            error: function (response) {
                            }
                        });
                    }//isConfirm
                    else {
                        swal("", "Cancelo el cierre", "error");
                    }
                });
            } else {
                swal("", "Debe seleccionar el mes para realizar el cierre", "error");
            }

        }


    </script>

@endsection