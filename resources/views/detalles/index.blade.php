@extends('layouts.master')
@section('title','PAC-Gestiones')
@section('breadcrumbs', Breadcrumbs::render('pac-gestion'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')


    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading clearfix">Gestiones
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="gestion_table"  cellspacing="0" style="display: none;">
                        <thead>
                        <th style="width: 90px;">Código_item</th>
                        <th>Item</th>
                        <th>Concepto</th>
                        <th>Responsable</th>
                        <th>Proveedor</th>
                        <th>Num_Factura</th>
                        <th>Fecha_Factura</th>
                        <th>Fecha_Entrega</th>
                        <th>Importe</th>
                        <th style="width: 60px;">Estado</th>
                        <th>Mes</th>
                        <th style="width: 60px;">Acción</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">Código_item</th>
                            <th class="search-filter">Item</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="search-filter">factura</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="search-filter">Estado</th>
                            <th class="search-filter">mes</th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($gestiones as $gestion)
                            <tr>
                                <td>{{$gestion->cod_programa.'-'.$gestion->cod_actividad.'-'.$gestion->cod_item}}</td>
                                <td>{{$gestion->item}}</td>
                                <td>{{$gestion->concepto}}</td>
                                <td>{{$gestion->nombres.' '.$gestion->apellidos}}</td>
                                <td>{{$gestion->proveedor}}</td>
                                <td>{{$gestion->num_factura}}</td>
                                <td>{{$gestion->fecha_factura}} </td>
                                <td>{{$gestion->fecha_entrega}} </td>
                                <td>$ {{$gestion->importe}}</td>
                                <td>
                                    @if ($gestion->estado=='Pendiente')
                                        <span class="label label-default">Pendiente</span>
                                    @else
                                        <span class="label label-success">Devengado</span>
                                    @endif
                                </td>
                                <td>{{$gestion->mes}} </td>
                                <td>
                                    @permission('gestion-procesos')
                                    @if($gestion->estado=='Pendiente' && Auth::user()->worker_id==$gestion->worker_id)
                                    <a href="{{route('admin.gestion.edit',$gestion->gestion_id)}}" class="btn btn-xs btn-success tip" data-placement="top" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                    <a href="#!" class="btn btn-xs btn-danger delete tip" data-placement="top" title="Eliminar"
                                       data-id="{{$gestion->gestion_id}}"><i class="fa fa-trash-o"  aria-hidden="true"></i>
                                    </a>
                                    @endif
                                    @endpermission

                                    @permission('aprueba-devengado')
                                        @if($gestion->estado=='Pendiente')
                                        <a href="#!" class="btn btn-xs btn-primary devengar tip" data-placement="top" title="Devengado"
                                           data-id="{{$gestion->gestion_id}}"><i class="fa fa-check-square-o" aria-hidden="true"></i>
                                        </a>
                                        @endif
                                    @endpermission
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

    {!! Form::open(['route'=>['admin.gestion.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
    {!! Form::close() !!}

    {!! Form::open(['route'=>['confirmarDevengado',':ID'],'method'=>'GET','id'=>'form-devengado']) !!}
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

            var table=$("#gestion_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                "language":{
                    "decimal":        "",
                    "emptyTable":     "No se encontraron datos en la tabla",
                    "info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered":   "(filtrados de un total _MAX_ registros)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encrontraron coincidencias",
                    "paginate": {
                        "first":      "Primero",
                        "last":       "Ultimo",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": Activar para ordenar ascendentemente",
                        "sortDescending": ": Activar para ordenar descendentemente"
                    }
                }
            });

            $("#gestion_table").fadeIn();


            $('#gestion_table .search-filter').each( function () {
                var title = $(this).text();
                $(this).html( '<input type="text" style="width: 100%" placeholder="'+title+'" />' );
            } );

            table.columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that.search( this.value ).draw();
                    }
                } );
            } );

        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-delete")
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                        title: "Confirme para eliminar !",
                        text: "Se eliminará la gestión!. Esta acción no se podrá deshacer!",
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
                                url:url,
                                data:data,
                                type: 'POST',
                                success: function (response) {
                                    swal("Confirmado!", response.message,"success");
                                    row.fadeOut();
                                },
                                error: function (response) {
                                    row.show();
                                    swal("ERROR!", response,"error");
                                }
                            });
                        }//isConfirm
                        else {
                            swal("Cancelado", "Canceló la acción :)", "error");
                        }
                    });
        });


        $(document).on('click','.devengar',function(e){
            e.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-devengado")
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                        title: "Devengar Importe !",
                        text: "El importe del proceso actual pasara de estado ejecutado a devengado!",
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
                                url:url,
                                data:data,
                                type: 'GET',
                                success: function (response) {
                                    if (response.tipo == 'error') {
                                        swal("ERROR!",response.message,"error");
                                    } else {
                                        swal("", response.message, "success");
                                    }
                                    $(".sa-confirm-button-container .confirm").on('click', function () {
                                        window.setTimeout(function () {location.reload()}, 1)});

                                },
                                error: function (response) {
                                    swal("ERROR!", response,"error");
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