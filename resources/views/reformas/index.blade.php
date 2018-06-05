@extends('layouts.master')
@section('tile','Reformas')
@section('breadcrumbs', Breadcrumbs::render('reformas'))

@section('content')
    <div class="row">
        <div class="col-sm-6">
            @include('alert.request')
            @include('alert.alert')
            @include('alert.alert_json')
        </div>
    </div>

    {!! Form::open(['route'=>'admin.reportes.reformas_select-pdf','method'=>'post','id'=>'imp_reformas_select']) !!}
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-condensed table-hover" id="reformas_table"
               cellspacing="0" style="display: none; font-size: 10px;" data-order='[[ 0, "desc" ]]'>
            <thead>
            <tr>
                <th style="width: 20px;">No.</th>
                <th>Dirección</th>
                <th>Ejecutor</th>
                <th style="width: 70px;">Valor_Origen</th>
                <th style="width: 60px;">Mes</th>
                <th style="width: 70px;">Código</th>
                <th>Nomb. Item</th>
                <th style="width: 70px;">Valor_Destino</th>
                <th style="width: 90px;">Monto/Mes</th>
                <th style="width: 70px;">Cod Destino</th>
                <th style="width: 70px;">Tipo</th>
                <th style="width: 50px;">Estado</th>
                <th style="width: 45px;">
                    {{--Check de seleccionar todos los check para informe de la pagina que se muestra--}}
                    {!! Form::checkbox('inf_all',null,false,['id'=>'inf_all','class'=>'checkbox tip','data-placement'=>'top', 'title'=>'Seleccionar todo']) !!}
                    {{--{!! Form::label('inf_all','Informes') !!}--}}
                    {{--Crear informe tecnico de las reformas agrupadas con los check--}}
                    @permission('imprimir-reformas')
                    {!! Form::button('<i class="fa fa-file-word-o" aria-hidden="true"></i>',['class'=>'btn-xs btn-info tip','data-placement'=>'top', 'title'=>'Generar Informe','type'=>'submit','id'=>'gen_informe','target'=>'_blank','name'=>'gen_informe','value'=>'gen_informe']) !!}
                    @endpermission
                </th>
                <th style="width: 70px;">Acción</th>
                <th  style="width: 45px;">
                    {{--Check de seleccionar todos los check para informe d ela pagina que se muestra--}}
                    {!! Form::checkbox('imp_all',null,false,['id'=>'imp_all','class'=>'checkbox tip','data-placement'=>'top', 'title'=>'Seleccionar todo']) !!}
                    {{--{!! Form::label('imp_all','Matriz') !!}--}}
                    {{--Expoprtar en excel la matriz de las reformas seleccionadas con los check--}}
                    @permission('imprimir-reformas')
                    {!! Form::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i>',['class'=>'btn-xs btn-success tip','data-placement'=>'top', 'title'=>'Imprimir Matriz Reformas','type'=>'submit','id'=>'imp_all_excel','target'=>'_blank','name'=>'imp_all_excel','value'=>'imp_all_excel']) !!}
                    @endpermission
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td class="tfoot_search">No.</td>
                <td class="tfoot_select"></td>
                <td></td>
                <td></td>
                <td class="tfoot_search">Mes</td>
                <td class="tfoot_search">Código Orig.</td>
                <td class="tfoot_search">Item</td>
                <td></td>
                <td></td>
                <td class="tfoot_search">Código Dest.</td>
                <td class="tfoot_select"></td>
                <td class="tfoot_select"></td>
                <td align="center" class="tfoot_search"></td>
                <td></td>
                <td>
                    {{--Expoprtar en pdf la matriz de las reformas seleccionadas con los check--}}
                    {{--@permission('imprimir-reformas')--}}
                    {{--{!! Form::button('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',['class'=>'btn-xs btn-danger tip','data-placement'=>'top', 'title'=>'Imprimir PDF','type'=>'submit','id'=>'imp_all_pdf','target'=>'_blank','name'=>'imp_all_pdf','value'=>'imp_all_pdf']) !!}--}}
                    {{--@endpermission--}}
                </td>
            </tr>
            </tfoot>
            <tbody>
            @foreach($reformas as $reforma)
                {{--Si el trabajador pertenece al area que se realizo la reforma y es analista o responsable-poa,
                              o  es root o administrador, mostrarlo--}}

                @if ( ( (Auth::user()->worker->departamento->area_id==$reforma->aiID && (Auth::user()->hasRole('analista') || Auth::user()->hasRole('responsable-poa'))) ) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador')))
                    <tr>
                        <td>{{$reforma->id}}</td>
                        <td>{{$reforma->area}}</td>
                        <td>{{$reforma->nombres.' '.$reforma->apellidos}} </td>
                        <td>$ {{$reforma->monto_orig}}</td>
                        <td>{{$reforma->mes}}</td>
                        <td>{{$reforma->cod_programa.'-'.$reforma->cod_actividad.'-'.$reforma->cod_item}}</td>
                        <td>{{$reforma->item}}</td>
                        <td>${{$reforma->total_destino}}</td>
                        <td>
                            @foreach($reforma->pac_destino as $pd)
                                ${{$pd->valor_dest.' / '.$pd->pac->meses->month}}<br/>
                            @endforeach
                        </td>
                        <td>
                            @foreach($reforma->pac_destino as $pd)
                                {{$pd->pac->area_item->item->cod_programa.'-'.$pd->pac->area_item->item->cod_actividad.'-'.$pd->pac->area_item->item->cod_item}}
                                <br/>
                            @endforeach
                        </td>
                        <td>{{$reforma->tipo_reforma}}</td>
                        @if ($reforma->estado==\App\Reforma::REFORMA_PENDIENTE)
                            <td style="color: #ff4b1c">
                                Pendiente
                            </td>
                        @elseif($reforma->estado==\App\Reforma::REFORMA_APROBADA)
                            <td style="color:#108114">
                                Aprobada
                            </td>
                        @endif
                        <td align="center">
                        {{--@if($reforma->estado==\App\Reforma::REFORMA_APROBADA)--}}
                                {{--<a href="#">--}}
                                    {{--{!! Form::button('<i class="fa fa fa-download" aria-hidden="true"></i>',['class'=>'btn-xs btn-success tip','data-placement'=>'top', 'title'=>'Descargar Informe','type'=>'submit','target'=>'_blank']) !!}--}}
                                {{--</a>--}}
                        {{--@endif--}}
                            @if(is_null($reforma->informe_id))
                            <a href="#!">
                                {!! Form::checkbox('select_informes[]',$reforma->id,false,['id'=>'I'.$reforma->id]) !!}
                                {{--{!! Form::label($reforma->id, $reforma->id) !!}--}}
                            </a>
                                @else
                                <a href="{{route('admin.informe.tecnico',$reforma->informe_id)}}" class="btn btn-xs btn-primary tip" data-placement="top" title="Descargar">{{$reforma->informe->codificacion.'-'.$reforma->informe->numero}}
                                </a>
                            @endif
                        </td>
                        <td>
                            @permission('admin-reformas')
                            @if ($reforma->estado== \App\Reforma::REFORMA_PENDIENTE)
                                <a href="#!" class="btn btn-xs btn-primary tip aprobar" data-placement="top"
                                   title="Aprobar"
                                   data-id="{{$reforma->id}}"><i
                                            class="fa fa-check-square-o" aria-hidden="true"></i>
                                </a>
                                <a href="#!" class="btn btn-xs btn-danger tip delete" data-placement="top"
                                   title="Cancelar"
                                   data-id="{{$reforma->id}}"><i class="fa fa-ban" aria-hidden="true"></i>
                                </a>
                            @endif
                            @endpermission
                            <a href="#!" class="btn btn-xs btn-info tip" data-placement="top" title="Detalle"
                               {{--data-toggle="modal" data-target="#show-modal" --}}
                               onclick="showDetalles({{$reforma->id}})"><i class="fa fa-eye"></i>
                            </a>
                            {{--@permission('imprimir-reformas')--}}
                            {{--<a href="{{route('admin.reportes.reforma-pdf',$reforma->id)}}"--}}
                            {{--class="btn btn-xs btn-warning tip" data-placement="top" title="Imprimir" target="_blank">--}}
                            {{--<i class="fa fa-file-pdf-o"></i>--}}
                            {{--</a>--}}
                            {{--@endpermission--}}
                            {{--<a href="#!" class="btn btn-xs btn-success tip" data-placement="top" title="Editar">--}}
                            {{--<i class="fa fa-edit"></i>--}}
                            {{--</a>--}}
                        </td>

                        <td align="center">
                            @permission('imprimir-reformas')
                            <a href="#!">
                                {!! Form::checkbox('imp_reformas[]',$reforma->id,false,['id'=>'R'.$reforma->id]) !!}
                                {{--{!! Form::label($reforma->id, $reforma->id) !!}--}}
                            </a>
                            @endpermission
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    {!! Form::close() !!}

    @include('reformas.show')

    {!! Form::open(['route'=>['admin.reformas.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
    {!! Form::close() !!}

    {!! Form::open(['route'=>['admin.reformas.confirm',':ID'],'method'=>'POST','id'=>'form-aprobar']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')

    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.which === 13) {
                    return false;
                }
            });

            //texto de input para filtrar
            $('.tfoot_search').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
            });

            var table = $("#reformas_table").DataTable({
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
                "columnDefs": [
                    { "orderable": false, "targets": [8,12,13,14] }
                ]
                ,
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
            $("#reformas_table").fadeIn();
        });

        //seleccionar todos los check para excel
        $(document).on('change', '#imp_all', function (event) {
            $("input[name='imp_reformas[]']").prop('checked', $(this).prop("checked"));
        });

        //seleccionar todos los check para informe
        $(document).on('change', '#inf_all', function (event) {
            $("input[name='select_informes[]']").prop('checked', $(this).prop("checked"));
        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(document).on('click', '#gen_informe', function (event) {
            $(this).prop('hidden', true);
//            setTimeout(function () {
//                    $("#gen_informe").prop('hidden', true);
//                    }, 50);
        });


        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var row = $(this).parents('tr');
            var id = $(this).attr('data-id');
            var form = $("#form-delete")
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                title: "Cancelar reforma!",
                text: "Eliminara la reforma. Esta acción no se podrá deshacer!",
                type: "info",
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
                        url: url,
                        data: data,
                        type: 'POST',
                        success: function (response) {
                            swal("", response.message, "success");
                            row.fadeOut();
                        },
                        error: function (response) {
                            row.show();
                            swal("ERROR!", response, "error");
                        }
                    });
                }// .end if isConfirm
                else {
                    swal("Cancelado", "Canceló la eliminación de la reforma :)", "error");
                }
            });// .end if isConfirm
        });

        $(document).on('click', '.aprobar', function (e) {
            e.preventDefault();
            var row = $(this).parents('tr');
            var id = $(this).attr('data-id');
            var form = $("#form-aprobar")
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                title: "Aprobar reforma!",
                text: "Aprobará la reforma. Se actualizarán los valores en los items",
                type: "info",
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
                        url: url,
                        data: data,
                        type: 'GET',
                        success: function (response) {
                            if (response.tipo == 'error') {
                                swal("ERROR!", response.message, "error");
                            } else {
                                swal("", response.message, "success");
                            }
                            $(".sa-confirm-button-container .confirm").on('click', function () {
                                window.setTimeout(function () {
                                    location.reload()
                                }, 1)
                            });

                        },
                        error: function (response) {
                            swal("ERROR!", response, "error");
                        }
                    });
                }// .end if isConfirm
                else {
                    swal("Cancelado", "Canceló la aprobación de la reforma :)", "error");
                }
            });// .end if isConfirm
        });

        //Mostrar detalles de la reforma show
        var showDetalles = function (id) {
            var url = "{{route('admin.reformas.show','REFORMA:ID')}}";
            var route = url.replace('REFORMA:ID', id);
            var token = $("input[name=_token]").val();
            var list = $("#detalle-reforma");
//            $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#show-detalle").modal('toggle');
                    list.html(response);
                },
                error: function (response) {
                }
            });
        };

    </script>
@endsection