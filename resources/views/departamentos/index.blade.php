@extends('layouts.master')
@section('title','Coordinaciones')
@section('breadcrumbs', Breadcrumbs::render('departamento'))

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
                    @permission('admin-coordinaciones')
                    <a href="#!" class="btn btn-primary tip" data-placement="top" title="Crear Coordinación"
                       data-toggle="modal" data-target="#create-modal">
                        Crear
                        <i class="fa fa-plus"aria-hidden="true"></i>
                    </a>
                    @endpermission
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="col-md-8 col-lg-offset-1">
        <table class="table" id="departamento_table" style="display: none;">
            <thead>
            <tr>
                <th>Dirección</th>
                <th>Coordinación</th>
                <th width="50px">Acción</th>
            </tr>
            </thead>
            <tbody>
            @foreach($departamentos as $dep)
                <tr>
                    <td>{{$dep->area->area}}</td>
                    <td>{{$dep->departamento}}</td>
                    <td>
                        @permission('admin-coordinaciones')

                        @if (Auth::user()->hasRole('root')||Auth::user()->hasRole('administrador'))
                            <a href="#!" class="btn btn-sm btn-danger deleteDep tip" data-placement="top"
                               title="Eliminar"
                               data-id="{{$dep->id}}"><i class="fa fa-trash-o"></i>
                            </a>
                            <a href="#!" class="btn btn-sm btn-success tip editDep" data-placement="top" title="Editar"
                               data-toggle="modal"
                               data-target="#edit-modal" onclick="mostrarEdit({{$dep->id}})"><i
                                        class="fa fa-pencil"></i>
                            </a>
                        @elseif (Auth::user()->hasRole('responsable-poa'))

                            {{--@if (Auth::user()->worker->departamento->area_id==$dep->area->id)--}}
                                {{--<a href="#!" class="btn btn-sm btn-danger deleteDep tip" data-placement="top"--}}
                                   {{--title="Eliminar"--}}
                                   {{--data-id="{{$dep->id}}"><i class="fa fa-trash-o"></i>--}}
                                {{--</a>--}}
                            {{--@endif--}}

                            @if (Auth::user()->worker->departamento->area_id==$dep->area->id)
                                <a href="#!" class="btn btn-sm btn-success tip editDep" data-placement="top"
                                   title="Editar" data-toggle="modal"
                                   data-target="#edit-modal" onclick="mostrarEdit({{$dep->id}})"><i
                                            class="fa fa-pencil"></i>
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
    @include('departamentos.edit')
    @include('departamentos.create')

    {!! Form::open(['route'=>['admin.departamentos.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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

        $(document).on('click', '.deleteDep', function (event) {
            event.preventDefault();
            var row = $(this).parents('tr');
            var id = $(this).attr('data-id');
            var form = $("#form-delete");
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                    title: "Eliminar Coordinación",
                    text: "Confirme para eliminar el departamento!",
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
                                swal("", response.message, "success");
                                row.fadeOut();
                            },
                            error: function (response) {
                                row.show();
                                swal("ERROR!", response, "error");
                            }
                        });
                    }//isConfirm
                    else {
                        swal("", "Canceló la eliminación del departamento :)", "error");
                    }
                });
        });

        //Modal mostrar editar departamento
        var mostrarEdit = function (id) {
            var url = "{{route('admin.departamentos.edit',':ID')}}"
            var route = url.replace(':ID', id);
            var token = $("input[name=_token]").val();
            $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#area_edit").val(response.area_id);
                    $("#dep_edit").val(response.departamento);
                    $("#dep_id").val(response.id);
                },
                error: function (response) {
                }
            });
        };

        //Actualizar departamento
        $(document).on('click', '.actualizarDep', function (event) {
            var id = $("#dep_id").val();
            var area = $("#area_edit").val();
            var dep = $("#dep_edit").val();
            var data = {area: area, departamento: dep};
            var url = "{{route('admin.departamentos.update',':ID')}}";
            var route = url.replace(':ID', id);
            $(this).ajaxPost(route, 'PUT', '{{route('admin.departamentos.index')}}', data);
            $("#edit-modal").modal('toggle');
        });


        function cargarDatatable() {
            var table = $("#departamento_table").DataTable({
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
            $("#departamento_table").fadeIn();
        }

        //Renderizar mensajes success o de error************************************//
        function ajaxRenderSection(url, response) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('.panel-body').empty().append($(data));
                    cargarDatatable();
                    if (response.estado == 'success') {
                        showSucces(response.message);
                    } else {
                        showError(response.message);
                    }
                },
                error: function (data) {
                }
            });
        }
        function ajaxRenderSectionError(url, response) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('.panel-body').empty().append($(data));
                    cargarDatatable();
                    var errors = response.responseJSON;
                    var error = '';
                    $.each(errors, function (i) {
                        error += errors[i] + '<br>';
                    });
                    showError(error);
                },
                error: function (data) {
                }
            });
        }
        $.fn.ajaxPost = function (url, method, sectionToRender, data) {
            var token = $("input[name=_token]").val();
            $.ajax({
                type: method,
                url: url,
                data: data,
                headers: {'X-CSRF-TOKEN': token},
                dataType: 'json',
                success: function (response) {
                    ajaxRenderSection(sectionToRender, response);
                },
                error: function (response) {
                    ajaxRenderSectionError(sectionToRender, response);
                }
            });
        };
        function showError(errors) {
            $("#msj-error").html(errors);
            $("#message-danger").fadeIn();
        }
        function showSucces(message) {
            $("#msj-ok").html(message);
            $("#message-success").fadeIn();
        }

    </script>
@endsection