@extends('layouts.master')
@section('title','Trabajadores')
@section('breadcrumbs', Breadcrumbs::render('trabajador'))

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
                @permission('admin-trabajadores')
                <a href="#!" class="btn btn-primary tip" data-placement="top" title="Crear Trabajador" data-toggle="modal"
                   data-target="#create-modal">Crear <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
                @endpermission
            </div>
        </div>
    </div>
</div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table" id="worker_table" style="display: none;" width="100%">
                <thead>
                <tr>
                    <th>Trabajador</th>
                    <th>Coordinación</th>
                    <th>Dirección</th>
                    <th>Correo</th>
                    <th>Cédula</th>
                    <th>Cargo</th>
                    <th width="50px">Acción</th>
                </tr>
                </thead>
                <tbody>
                @foreach($workers as $worker)
                    @if ($worker->user->hasRole('root') && !Auth::user()->hasRole('root'))
                        @continue
                    @endif
                    <tr>
                        <td>{{$worker->nombres}} {{$worker->apellidos}}</td>
                        <td>{{$worker->departamento->departamento}}</td>
                        <td>{{$worker->departamento->area->area}}</td>
                        <td>{{$worker->email}}</td>
                        <td>{{$worker->num_doc}}</td>
                        <td>{{$worker->cargo}}</td>
                        <td>
                            @permission('admin-trabajadores')

                            @if (Auth::user()->hasRole('root')||Auth::user()->hasRole('administrador'))
                            <a href="#!" class="btn btn-sm btn-danger deleteWorker tip" data-placement="top"
                               title="Eliminar" data-id="{{$worker->id}}"><i class="fa fa-trash-o"></i></a>
                            <a href="#!" class="btn btn-sm btn-success tip" data-placement="top" title="Editar"
                               data-toggle="modal" data-target="#edit-modal" onclick="mostrarEdit({{$worker->id}})"><i
                                        class="fa fa-pencil"></i>
                            </a>
                            @elseif (Auth::user()->hasRole('responsable-poa'))

                                @if (Auth::user()->worker->departamento->area_id==$worker->departamento->area_id)
                                    <a href="#!" class="btn btn-sm btn-danger deleteWorker tip" data-placement="top"
                                       title="Eliminar" data-id="{{$worker->id}}"><i class="fa fa-trash-o"></i></a>
                                    <a href="#!" class="btn btn-sm btn-success tip" data-placement="top" title="Editar"
                                       data-toggle="modal" data-target="#edit-modal" onclick="mostrarEdit({{$worker->id}})"><i
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
    </div>
    @include('workers.edit')
    @include('workers.create')

    {!! Form::open(['route'=>['admin.workers.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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

        //al cambiar area
        $(document).on('change', '.area_id', function (e) {
            var id = this.value;
            var token = $("input[name=_token]").val();
            var route = "{{route('getDpto')}}";
            var dpto = $(".departamento");
            var load=$(".load_dpto_create");
            load.removeClass('hidden');
            dpto = $(".departamento").prop('disabled',false);
            var data = {
                area_id: id
            };
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
                data: data,
                success: function (response) {
                    dpto.find("option:gt(0)").remove();
                    for (i = 0; i < response.length; i++) {
                        dpto.append('<option value="' + response[i].id + '">' + response[i].departamento + '</option>');
                    }
                    load.addClass('hidden');
                },
                error: function (response) {
                }
            });
        });

        $(document).on('click', '.deleteWorker', function (event) {
            event.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-delete");
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                        title: "Eliminar trabajador!",
                        text: "Confirme para eliminar al trabajador. Esta acción no se podrá deshacer!",
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
                                type: 'POST',
                                success: function (response) {
                                    swal("", response.message,"success");
                                    row.fadeOut();
                                },
                                error: function (response) {
                                    row.show();
                                    swal("ERROR!", response,"error");
                                }
                            });
                        }//isConfirm
                        else {
                            swal("Cancelado", "Canceló la eliminación del trabajador :)", "error");
                        }
                    });


        });

        //Modal editar
        var mostrarEdit = function (id) {
            var url = "{{route('admin.workers.edit',':ID')}}";
            var route = url.replace(':ID', id);
            var token = $("input[name=_token]").val();
            // $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#worker_id").val(response.worker.id);
                    $("#area_edit").val(response.area.id);
                    $("#departamento_edit").val(response.worker.departamento_id);
                    $("#nombres_edit").val(response.worker.nombres);
                    $("#apellidos_edit").val(response.worker.apellidos);
                    $("#email_edit").val(response.worker.email);
                    $("#num_doc_edit").val(response.worker.num_doc);
                    $("#cargo_edit").val(response.worker.cargo);
                    $("#tratamiento_edit").val(response.worker.tratamiento);
                },
                error: function (response) {
                }
            });
        };

        $(document).on('click', '.actualizarWorker', function (event) {
            var id = $("#worker_id").val();
            var dep = $("#departamento_edit").val();
            var area = $("#area_edit").val();
            var nomb = $("#nombres_edit").val();
            var ape = $("#apellidos_edit").val();
            var email = $("#email_edit").val();
            var num = $("#num_doc_edit").val();
            var cargo = $("#cargo_edit").val();
            var tratamiento = $("#tratamiento_edit").val();
            var data = {
                dep_id: dep,
                area_id: area,
                nombres: nomb,
                apellidos: ape,
                email: email,
                num_doc: num,
                cargo: cargo,
                tratamiento: tratamiento
            };
            var url = "{{route('admin.workers.update',':ID')}}";
            var route = url.replace(':ID', id);
            $(this).ajaxPost(route, 'PUT', '{{route('admin.workers.index')}}', data);
            $("#edit-modal").modal('toggle');
        });

        function cargarDatatable() {
            var table = $("#worker_table").DataTable({
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
            $("#worker_table").fadeIn();
        }

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
                    var errors = data.responseJSON;
                    var error = '';
                    if (errors) {
                        $.each(errors, function (i) {
//                            console.log(errors[i]);
                            error += errors[i] + '<br>';
                        });
                        showError(error);
                    }
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
                    ajaxRenderSection(sectionToRender,response);
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    var error = '';
                    if (errors) {
                        $.each(errors, function (i) {
                            //console.log(errors[i]);
                            error += errors[i] + '<br>';
                        });
                        showError(error);
                    }
//                    ajaxRenderSection(sectionToRender);
                }
            });
        }

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