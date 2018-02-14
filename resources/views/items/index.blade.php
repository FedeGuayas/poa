@extends('layouts.master')
@section('title','Items')
@section('breadcrumbs', Breadcrumbs::render('item'))

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            @include('alert.alert_json')
            @include('alert.request')
            @include('alert.alert')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
            <div class="col-lg-6">
                <div class="form-group">
                    @permission('admin-items')
                    <a href="#!" class="btn btn-primary tip" data-placement="top" title="Crear Item" data-toggle="modal" data-target="#create-modal">Crear <i class="fa fa-plus" aria-hidden="true"></i></a>
                    @endpermission
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="col-xs-12 ">
        <div class="table-responsive">
            <table class="table" id="items_table" style="display: none;">
                <thead>
                <tr>
                    <th style="width: 40px;">Programa</th>
                    <th style="width: 40px;">Actividad</th>
                    <th style="width: 40px;">Cód_Item</th>
                    <th>Item</th>
                    <th>Presupuesto</th>
                    {{--<th>Disponible</th>--}}
                    <th>Acción</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th class="search-filter"></th>
                    <th class="search-filter"></th>
                    <th></th>
                    {{--<th></th>--}}
                    <th></th>
                </tr>
                </tfoot>
                <tbody>

                @if (count($item_list)>0)
                    @foreach($item_list as $item)
                        <tr>
                            <td>{{$item->cod_programa}}</td>
                            <td>{{$item->cod_actividad}}</td>
                            <td >{{$item->cod_item}}</td>
                            <td>{{$item->item}}</td>
                            <td>${{number_format($item->presupuesto,2,'.',' ') }}</td>
                            {{--<td>${{number_format($item->disponible,2,'.',' ')}}</td>--}}
                            <td width="10%">
                                @permission('admin-items')
                                <a href="#!" class="btn btn-sm btn-danger delete tip" data-placement="top" title="Eliminar"
                                   data-id="{{$item->id}}"><i class="fa fa-trash-o"></i></a>
                                <a href="#!" class="btn btn-sm btn-success tip" data-placement="top" title="Editar"
                                   data-toggle="modal" data-target="#edit-modal-{{$item->id}}"><i class="fa fa-pencil"></i>
                                </a>
                                @endpermission
                            </td>
                        </tr>
                        @include('items.edit')
                    @endforeach
                @endif

                </tbody>
            </table>
        </div>
    </div>

    @include('items.create')

    {!! Form::open(['route'=>['admin.items.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')
    <script>

        $(document).ready(function () {

            cargarDatatable();
            $(".form_noEnter").keypress(function (e) {
                if (e.width == 13) {
                    return false;
                }
            });

        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(document).on('click', '.guardar', function (event) {
            event.preventDefault();
            var data = {
                actividad: $("#actividad").val(),
                programa: $("#programa").val(),
                codigo: $("#codigo").val(),
                item: $("#item").val(),
                presupuesto: $("#presupuesto").val()
            };
           $(this).ajaxPost('{{route('admin.items.store')}}', 'POST', '{{route('admin.items.index')}}', data);
            $("#create-modal").modal('toggle');
        });

        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var row = $(this).parents('tr');
            var id = $(this).attr('data-id');
            var form = $("#form-delete")
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                title: "Confirme para eliminar el item!",
                text: "Seguro que quiere eliminar el item?. Esta acción no se podrá deshacer!",
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
                            swal("Confirmado!", response.message, "success");
                            row.fadeOut();
                        },
                        error: function (response) {
                            row.show();
                            console.log(response);
                            swal("ERROR!", response, "error");
                        }
                    });
                }// .end if isConfirm
                else {
                    swal("Cancelado", "Canceló la eliminación del item :)", "error");
                }
            });// .end if isConfirm
        });

        //al cambiar programa
        $(document).on('change', '#programa', function (e) {
            var id = this.value;
            var token = $("input[name=_token]").val();
            var route = "{{route('poaFDG')}}";
            var act = $("#actividad");
            var load=$(".load_dpto_create");
            load.removeClass('hidden');
            var data = {
                prog_id: id
            };
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
                data: data,
                success: function (response) {
                    act.find("option:gt(0)").remove();
                    for (i = 0; i < response.length; i++) {
                        act.append('<option value="' + response[i].id + '">' + response[i].cod_actividad + ' - ' + response[i].actividad + '</option>');
                    }
                    load.addClass('hidden');
                },
                error: function (response) {
                    load.addClass('hidden');
                    act.find("option:gt(0)").remove();
                }
            });
        });

        //al cambiar programa_edit
        $(document).on('change', '#programa_edit', function (e) {
            var id = this.value;
            var token = $("input[name=_token]").val();
            var route = "{{route('poaFDG')}}";
            var act = $("#actividad_edit");
            var data = {
                prog_id: id
            };
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
                data: data,
                success: function (response) {
                    act.find("option:gt(0)").remove();
                    for (i = 0; i < response.length; i++) {
                        act.append('<option value="' + response[i].id + '">' + response[i].cod_actividad + ' - ' + response[i].actividad + '</option>');
                    }
                },
                error: function (response) {
                }
            });
        });

        {{--$(document).on('click', '.actualizarItem', function (event) {--}}
            {{--var id = $("#item_id").val();--}}
            {{--var codigo = $("#codigo_edit").val();--}}
            {{--var item = $("#item_edit").val();--}}
            {{--var presupuesto=$("#presupuesto_edit").val();--}}
            {{--var programa=$("#programa_edit").val();--}}
            {{--var actividad=$("#actividad_edit").val();--}}
            {{--var data = {cod: codigo, item: item, presupuesto:presupuesto, programa:programa, actividad:actividad};--}}
            {{--var url = "{{route('admin.items.update',':ID')}}";--}}
            {{--var route = url.replace(':ID', id);--}}
            {{--$(this).ajaxPost(route, 'PUT', '{{route('admin.items.index')}}', data);--}}
            {{--$(this).modal('toggle');--}}
        {{--});--}}

        function cargarDatatable(){
            var table=$("#items_table").DataTable({
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
            $("#items_table").fadeIn();



            $('#items_table .search-filter').each( function () {
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
        }

        //Renderizar mensajes success o de error************************************//
        function ajaxRenderSection(url,response) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('.panel-body').empty().append($(data));
                    cargarDatatable();
                    if (response.estado=='success'){
                        showSucces(response.message);
                    }else {
                        showError(response.message);
                    }
                },
                error: function (data) {
                }
            });
        }
        function ajaxRenderSectionError(url,response) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('.panel-body').empty().append($(data));
                    cargarDatatable();
                    var errors = response.responseJSON;
                    var error='';
                    $.each(errors, function (i) {
                        error += errors[i] + '<br>';
                    });
                    showError(error);
                },
                error: function (data) {
                }
            });
        }
        $.fn.ajaxPost = function(url,method,sectionToRender,data) {
            var token = $("input[name=_token]").val();
            $.ajax({
                type: method,
                url: url,
                data:data,
                headers: {'X-CSRF-TOKEN': token},
                dataType: 'json',
                success: function (response) {
                    ajaxRenderSection(sectionToRender,response);
                },
                error: function (response) {
                    ajaxRenderSectionError(sectionToRender,response);
                }
            });
        };
        function showError(errors){
            $("#msj-error").html(errors);
            $("#message-danger").fadeIn();
        }
        function showSucces(message){
            $("#msj-ok").html(message);
            $("#message-success").fadeIn();
        }


    </script>

@endsection