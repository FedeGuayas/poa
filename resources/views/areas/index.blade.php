@extends('layouts.master')
@section('title','Direcciones')
@section('breadcrumbs', Breadcrumbs::render('area'))

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
                    @permission('admin-direcciones')
                    <a href="#!" class="btn btn-primary tip" data-placement="top" title="Crear Dirección" data-toggle="modal" data-target="#create-modal">Crear <i class="fa fa-plus" aria-hidden="true"></i></a>
                    @endpermission
                </div>
            </div>
        </div>
    </div>

    {{--{!! Form::open(['class'=>'form-inline form_noEnter', 'id'=>'form_area_store']) !!}--}}
    {{--<div class="form-group">--}}
        {{--{!! Form::label('area','Dirección',['class'=>'col-xs-12 col-sm-3 col-md-3 col-lg-3 control-label']) !!}--}}
        {{--{!! Form::text('area',null,['class'=>'form-control', 'placeholder'=>'Nombre de la dirección','style'=>'text-transform:uppercase']) !!}--}}
        {{--{!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i> Crear',['class'=>'btn btn-primary guardar tip','data-placement'=>'top', 'title'=>'Crear área']) !!}--}}
    {{--</div>--}}
    {{--{!! Form::close() !!}--}}
<hr>
<div class="col-md-8 col-lg-offset-2">
    <table class="table" id="area_table" style="display: none;">
        <thead>
        <tr>
            <th>Dirección</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach($areas as $area)
            <tr>
                <td>{{$area->area}}</td>
                <td>
                    @permission('admin-direcciones')
                    <a href="#!" class="btn btn-sm btn-danger deleteArea tip" data-placement="top" title="Eliminar" data-id="{{$area->id}}"><i class="fa fa-trash-o"></i></a>
                    <a href="#!" class="btn btn-sm btn-success tip edit" data-placement="top" title="Editar" data-toggle="modal" data-target="#edit-modal" onclick="mostrarEdit({{$area->id}})"><i class="fa fa-pencil"></i>
                    </a>
                    @endpermission
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

    @include('areas.edit')
    @include('areas.create')

    {!! Form::open(['route'=>['admin.areas.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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

        $(document).on('mouseover','.tip',function(event){
            $(this).tooltip();
        });

        $(document).on('click', '.deleteArea', function (event) {
            event.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-delete");
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                        title: "Eliminar Dirección!",
                        text: "Confirme para eliminar el área!",
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
                            swal("", "Canceló la eliminación del área :)", "error");
                        }
                    });

        });

        //Modal mostrar editar area
        var mostrarEdit= function(id){
            var url="{{route('admin.areas.edit','AREA:ID')}}"
            var route=url.replace('AREA:ID',id);
            var token = $("input[name=_token]").val();
            $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#area_id").val(response.id);
                    $("#area_edit").val(response.area);
                },
                error: function (response) {
                }
            });
        };

        //actualizar area
        $(document).on('click','.actualizarArea',function(event){
            var id = $("#area_id").val();
            var area = $("#area_edit").val();
            var data={area: area};
            var url="{{route('admin.areas.update',':ID')}}";
            var route= url.replace(':ID',id);
            $(this).ajaxPost(route,'PUT','{{route('admin.areas.index')}}',data);
            $("#edit-modal").modal('toggle');
        });

        function cargarDatatable(){
            var table=$("#area_table").DataTable({
                lengthMenu: [[6, 10, -1], [6, 10, 'Todo']],
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
            $("#area_table").fadeIn();
        }


        //Renderizar mensajes success o de error************************************
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