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
               cellspacing="0" style="display: none;" data-order='[[ 0, "desc" ]]'>
            <thead>
            <th style="width: 40px;">No.</th>
            <th style="width: 70px;">Dirección</th>
            <th style="width: 70px;">Valor_Origen</th>
            <th style="width: 90px;">Código</th>
            <th style="width: 70px;">Tipo</th>
            <th>Nomb. Item</th>
            <th style="width: 70px;">Mes</th>
            <th style="width: 70px;">Valor_Destino</th>
            <th>Ejecutor</th>
            <th style="width: 60px;">Estado</th>
            <th style="width: 120px;">Acción</th>
            <th>
                {!! Form::checkbox('imp_all',null,false,['id'=>'imp_all']) !!}
                {!! Form::label('imp_all','Todos') !!}
            </th>
            </thead>
            <tfoot>
            <tr>
                <th class="search-filter">filtrar</th>
                <th class="search-filter">filtrar</th>
                <th></th>
                <th class="search-filter">filtrar</th>
                <th class="search-filter">filtrar</th>
                <th class="search-filter">filtrar</th>
                <th class="search-filter">filtrar</th>
                <th></th>
                <th></th>
                <th class="search-filter">filtrar</th>
                <th></th>
                <th>
                    @permission('imprimir-reformas')
                    {!! Form::button('<i class="fa fa fa-file-pdf-o" aria-hidden="true"></i>',['class'=>'btn-xs btn-primary tip','data-placement'=>'top', 'title'=>'Imprimir Seleccionados','type'=>'submit','id'=>'imp_all','target'=>'_blank']) !!}
                    @endpermission
                </th>
            </tr>
            </tfoot>
            <tbody>
            @foreach($reformas as $reforma)
                <tr>
                    <td>{{$reforma->id}}</td>
                    <td>{{$reforma->area}}</td>
                    <td>$ {{$reforma->monto_orig}}</td>
                    <td>{{$reforma->cod_programa.'-'.$reforma->cod_actividad.'-'.$reforma->cod_item}}</td>
                    <td>{{$reforma->tipo}}</td>
                    <td>{{$reforma->item}}</td>
                    <td>{{$reforma->mes}}</td>
                    <td>${{$reforma->total_destino}}</td>
                    <td>{{$reforma->nombres.' '.$reforma->apellidos}} </td>
                    <td>
                        @if ($reforma->estado=='Pendiente')
                            <span class="label label-warning">Pendiente</span>
                        @else
                            <span class="label label-success">Aprobada</span>
                    @endif
                    <td>
                        @permission('admin-reformas')
                        @if ($reforma->estado=='Pendiente')
                            <a href="#!" class="btn btn-xs btn-primary tip aprobar" data-placement="top" title="Aprobar"
                               data-id="{{$reforma->id}}"><i
                                        class="fa fa-check-square-o" aria-hidden="true"></i>
                            </a>
                            <a href="#!" class="btn btn-xs btn-danger tip delete" data-placement="top" title="Cancelar"
                               data-id="{{$reforma->id}}"><i class="fa fa-ban" aria-hidden="true"></i>
                            </a>
                        @endif
                        @endpermission
                        <a href="#!" class="btn btn-xs btn-info tip" data-placement="top" title="Detalle"
                           {{--data-toggle="modal" data-target="#show-modal" --}}
                           onclick="showDetalles({{$reforma->id}})"><i class="fa fa-eye"></i>
                        </a>
                        @permission('imprimir-reformas')
                        <a href="{{route('admin.reportes.reforma-pdf',$reforma->id)}}"
                           class="btn btn-xs btn-warning tip" data-placement="top" title="Imprimir" target="_blank">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                        @endpermission
                        {{--<a href="#!" class="btn btn-xs btn-success tip" data-placement="top" title="Editar">--}}
                            {{--<i class="fa fa-edit"></i>--}}
                        {{--</a>--}}
                    </td>
                    <td>
                        @permission('imprimir-reformas')
                        <a href="#!">
                            {!! Form::checkbox('imp_reformas[]',$reforma->id,false,['id'=>$reforma->id]) !!}
                            {{--{!! Form::label($reforma->id, $reforma->id) !!}--}}
                        </a>
                        @endpermission
                    </td>
                </tr>
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
                if (e.width == 13) {
                    return false;
                }
            });

            var table = $("#reformas_table").DataTable({
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

            $("#reformas_table").fadeIn();

            $('#reformas_table .search-filter').each(function () {
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

        $(document).on('change', '#imp_all', function (event) {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
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
                    console.log(response);
                }
            });
        };

    </script>
@endsection