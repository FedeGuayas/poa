@extends('layouts.master')
@section('title','PAC-Procesos')
@section('breadcrumbs', Breadcrumbs::render('pac-proceso'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')
    @include('alert.request')


    <div class="col-md-12">
        {!! Form::open(['route'=>['admin.pacs.index'],'method'=>'GET','class'=>'form_noEnter', 'id'=>'form_search']) !!}
        <div class="form-inline">
            <div class="form-group">
                {{--{!! Form::label('area','Areas') !!}--}}
                {!! Form::select('area',$list_areas,$area_select,['class'=>'form-control','placeholder'=>'Direcciones...','id'=>'area']) !!}
            </div>
            {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Buscar','id'=>'buscar', 'type'=>'submit']) !!}
        </div>
        {!! Form::close() !!}

        <hr>

        <div class="panel panel-success">
            <div class="panel-heading clearfix">PAC - {{ count($area)>0 ? $area->area : "Direcciones" }}
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="row">
                    <div class="container col-lg-6" id="botones_imprimir">
                        {{--{!! Form::open(['route'=>['admin.pacs.pac-pdf'],'method'=>'GET','id'=>'form_imprimir']) !!}--}}
                        {{--<a href="#!" class="btn btn-default tip" type="submit" data-placement="top" title="Imprimir" target="_blank" id="imprimir_pdf"><i class="fa fa-print"></i></a>--}}
                        {{--{!! Form::close() !!}--}}
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"
                           cellspacing="0" style="display: none; font-size: 10px;">
                        <thead>
                        <th style="width: 60px">Código</th>
                        <th>Item</th>
                        <th>Dirección
                        </th>{{--Area del item presupuestario, coloreada si el area del responsable no es la misma, o sea si es un item compratido--}}
                        <th style="width: 60px">Mes</th>
                        <th style="width: 100px">Responsable</th>
                        <th>Procedimiento</th>
                        <th>Concepto</th>
                        <th>Presupuesto</th>
                        <th>Ejecutado</th>
                        <th>Devengado</th>
                        <th>Disponible</th>
                        <th>Cert. PAC</th>
                        <th style="width: 65px">Acción</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">filtrar</th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($pacs as $pac)
                            <tr>
                                <td>{{$pac->cod_programa.'-'.$pac->cod_actividad.'-'.$pac->cod_item}}</td>
                                <td>{{$pac->item}}</td>
                                <td>
                                    {{--verifico si presupuesto es del del area del trabajador o de otro poa compartido  --}}
                                    @if ($pac->area_trabajador == $pac->aiID)
                                        {{$pac->area}}
                                    @else
                                        {{--EL dinero es compartido con trabajadores de otra area--}}
                                        <strong class="text-danger">{{$pac->area}}</strong>
                                    @endif
                                </td>
                                <td>{{$pac->mes}}</td>
                                <td>{{$pac->nombres}} {{$pac->apellidos}}</td>
                                <td>{{$pac->procedimiento}}</td>
                                <td>{{$pac->concepto}}</td>
                                <td>$ {{$pac->presupuesto}}</td>
                                <td>$ {{$pac->comprometido}} </td>
                                <td>$ {{$pac->devengado}}</td>
                                <td>
                                    @if ($pac->reform== \App\Pac::PERMITIR_REFORMAR_PAC)
                                        <i class="text-success fa fa-check-circle tip" data-placement="top"
                                           title="Permitido reformas sobre este pac"> </i>
                                            $ {{$pac->disponible}}

                                    @else
                                        <i class="text-danger fa fa-ban tip" data-placement="top"
                                           title="No se permiten reformas sobre este pac"> </i>
                                            $ {{$pac->disponible}}
                                    @endif
                                </td>
                                <td>
                                    @permission('certificacion-pac')
                                    <a href="{{route('admin.pacs.certificacion-pac',$pac->id)}}"
                                       class="btn btn-xs btn-danger tip" data-placement="top"
                                       title="Generar Certificación" target="_blank">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </a>

                                    <a href="#subirCPAC-{{$pac->id}}" data-toggle="modal"
                                       class="btn btn-xs btn-success tip" data-placement="top"
                                       title="Subir Certificación" target="_blank">
                                        <i class="fa fa-upload"></i>
                                    </a>
                                    @endpermission
                                    @if (!is_null($pac->certificado_file))
                                        <a href="{{route('pac.CPACdownload',$pac->id)}}"
                                           class="btn btn-xs btn-primary tip" data-placement="top"
                                           title="Descargar Certificación">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @permission('gestion-procesos')
                                    @if($pac->disponible > 0)
                                        @if ((Auth::user()->worker_id==$pac->trabajador_id) && (!is_null($pac->certificado_file)) && ($pac->reform== \App\Pac::NO_REFORMAR_PAC))
                                            <a href="{{route('admin.gestion.create',$pac->id)}}"
                                               class="btn btn-xs btn-primary tip"
                                               data-placement="top" title="Gestión">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @endpermission

                                    <a href="{{route('admin.gestion.show',$pac->id)}}" class="btn btn-xs btn-info tip"
                                       data-placement="top" title="Ver Gestión"><i class="fa fa-eye"
                                                                                   aria-hidden="true"></i>
                                    </a>

                                    @permission('solicita-reformas')
                                    @if($pac->disponible > 0 && ($pac->reform== \App\Pac::PERMITIR_REFORMAR_PAC))
                                        {{--reformas solo a mes actual o superior--}}
                                        @if ($pac->cod >= $mes_actual)
                                            <a href="{{route('createReforma',$pac->id)}}"
                                               class="btn btn-xs btn-danger tip" data-placement="top"
                                               title="Solicitud de Reforma"><i class="fa fa-recycle"
                                                                               aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @endpermission
                                </td>
                            </tr>
                            @include('pac.subir-cpac-modal')
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

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

            $("#imprimir_pdf").on('click', function (event) {
                event.preventDefault();
                var token = $("input[name=_token]").val();
                var form = $("#form_search");
                var url = "{{route('admin.pacs.pac-pdf')}}";
                var data = form.serialize();
                $.ajax({
                    url: url,
                    data: data,
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    success: function (response) {
                    },
                    error: function (response) {
                        console.log(response);

                    }
                });

            });

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
                    },
                    "buttons": {
                        "colvis": "Columnas",
                        "copy": "Copiar",
                        "print": "Imprimir"
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
                    total_presupuesto = api.column(7).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_ejecutado = api.column(8).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_devengado = api.column(9).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_disponible = api.column(10).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // Total en la pagina actual
                    pageTotal_pre = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_eje = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(9, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_disp = api.column(10, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(7).footer()).html('$' + pageTotal_pre + '<p style="color: #0c199c">' + ' ( $' + total_presupuesto + ' )' + '</p>');
                    $(api.column(8).footer()).html('$' + pageTotal_eje + '<p style="color: #0c199c">' + ' ( $' + total_ejecutado + ' )' + '</p>');
                    $(api.column(9).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_devengado + ' )' + '</p>');
//                    $(api.column(10).footer()).html('$' + pageTotal_disp + '<p style="color: #0c199c">' + ' ( $' + total_disponible + ' )' + '</p>');
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
                columnDefs: [{
//                    targets: -1,
                    visible: false
                }]
            });
//
//            table.buttons().container()
//                    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
            table.buttons().container()
                .appendTo('#botones_imprimir');

            $("#pac_table").fadeIn();


            $('#pac_table .search-filter').each(function () {
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

        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

    </script>

@endsection