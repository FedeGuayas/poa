@extends('layouts.master')
@section('title','Cierre Mensual')
@section('breadcrumbs', Breadcrumbs::render('cierre'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')


    <div class="alert alert-danger" id="warning-alert" role="alert"
         style="display: none; position: fixed; right: 5%; margin-left: 5%; border: 1px solid; z-index: 9999; ">
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Atención! </strong>
        Antes de hacer el cierre verifique que tiene cargado el esigef correspondiente al mes que desea cerrar.
    </div>


    <div class="row">
        <div class="col-lg-3">
            <div class="form-inline">
                <div class="form-group col-lg-10">
                    {!! Form::select('mes',$list_meses,$mes,['class'=>'form-control selectpicker','placeholder'=>'Seleccione ...','id'=>'mes']) !!}
                </div>
                @permission('hacer-cierre')
                {!! Form::button('<i class="fa fa-clone" aria-hidden="true"></i>',['class'=>'btn btn-danger tip','data-placement'=>'top', 'title'=>'Hacer Cierre','id'=>'cierre']) !!}
                @endpermission
            </div>
        </div>
    </div>

    <hr>
    <div class="col-md-12">

        <div class="panel panel-success">
            <div class="panel-heading clearfix">Información de Esigef para cierre.
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-condensed table-hover" id="resumen_table"
                           cellspacing="0" style="display: none; font-size: 11px;" data-order='[[ 1, "asc" ]]'>
                        <thead>
                        <tr>
                            <th style="width: 65px">ItemID</th>
                            <th style="width: 65px">Código</th>
                            <th>Act.</th>
                            <th>Item</th>
                            <th>Codif_ESIGEF</th>
                            <th>Dev_ESIGEF</th>
                            <th>POA Plan.</th>
                            <th>POA Disp.</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter"></th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($esigef_items as $res)
                            <tr>
                                <td>{{$res->itemID}}</td>
                                <td>{{$res->esigefPrograma.'-'.$res->esigefActividad.'-'.$res->esigefItem}}</td>
                                <td>{{$res->actividad}}</td>
                                <td>{{$res->item}}</td>
                                <td>
                                    $ {{number_format(($res->esigefCodificado),2,'.','')}} </td>{{--Codificado de esigef--}}
                                <td>
                                    $ {{number_format(($res->esigefDevengado),2,'.','')}} </td>{{--de esigef, con los ingresos extras--}}
                                <td>
                                    $ {{number_format(($res->itemPresupuesto),2,'.','')}} </td>{{--Items, Plan inicial --}}
                                <td>
                                    $ {{number_format(($res->itemDisponible),2,'.','')}} </td>{{--Items que no se han repartido por areas--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}

    </div>{{--./col-md-12--}}

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    {{--{!! Form::close() !!}--}}


@endsection


@section('scripts')

    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.which === 13) {
                    return false;
                }
            });

            $("#warning-alert").fadeIn(2000).fadeTo(3000, 0.5).slideUp(2000, function () {
                //           $(this).slideUp(5000);
                $(this).remove();
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
                    total_codificado = api.column(4).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_devengado = api.column(5).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_plan = api.column(6).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_disp = api.column(7).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);


                    // Total en la pagina actual
                    pageTotal_cod = api.column(4, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_plan = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_disp = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(4).footer()).html('$' + pageTotal_cod + '<p style="color: #0c199c">' + ' ( $' + total_codificado + ' )' + '</p>');
                    $(api.column(5).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_devengado + ' )' + '</p>');
                    $(api.column(6).footer()).html('$' + pageTotal_plan + '<p style="color: #0c199c">' + ' ( $' + total_plan + ' )' + '</p>');
                    $(api.column(7).footer()).html('$' + pageTotal_disp + '<p style="color: #0c199c">' + ' ( $' + total_disp + ' )' + '</p>');


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
                if ($(this).val() === '' || $(this).val() === 'placeholder') {
                    $('#cierre').prop('disabled', true);
                } else  $('#cierre').prop('disabled', false);
//                var mes=$( "#mes option:selected" ).text();
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
            var route_update = "{{route('admin.actualizarHistorico')}}";
            var mes = $("#mes").val();
            var mes_name = $("#mes option:selected").text();
            var data = {
                mes: mes
            };
            if (mes != '') {
                swal({
                    title: "",
                    text: "Se realizará el cierre del mes de " + mes_name + " ! Seguro desea continuar?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "SI!",
                    cancelButtonText: " NO!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) { //confirma el cierre, store
                        $.ajax({
                            url: route,
                            type: "POST",
                            headers: {'X-CSRF-TOKEN': token},
                            dataType: 'json',
                            data: data,
                            success: function (response) {

                                if (response.tipo === "existe") { //actualizarHistorico
//                                    swal("Error", response.response, "warning");
                                     //Actualizar cierre existente? ya existe cierre del mes seleccionado
                                    /************************/
                                    swal({
                                        title: "",
                                        text: response.response ,
                                        type: "info",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55",
                                        confirmButtonText: "SI!",
                                        cancelButtonText: " NO!",
                                        closeOnConfirm: false,
                                        closeOnCancel: false,
                                        showLoaderOnConfirm: true
                                    }, function (isConfirm) {
                                        if (isConfirm) {//confirma atualizacion de cierre
                                            $.ajax({
                                                url: route_update,
                                                type: "POST",
                                                headers: {'X-CSRF-TOKEN': token},
                                                dataType: 'json',
                                                data: data,
                                                success: function (response) {
                                                    if (response.tipo == "error") { //actualizacion correcta
                                                        swal("Error", response.response, "error");
                                                    } else swal("", response.response, "success");
                                                },
                                                error: function (response) {
                                                }
                                            });
                                        }//isConfirm
                                        else {
                                            swal("", "Cancelo la actualización del historico", "error");
                                        }
                                    });
                                    //Fin Actualizar cierre existente
                                    /************************/
                                }else if (response.tipo === "error") {
                                    swal("Error", response.response, "error");
                                }
                                else swal("", response.response, "success");
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