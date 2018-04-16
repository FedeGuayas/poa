@extends('layouts.master')
@section('title','Programas')
@section('breadcrumbs', Breadcrumbs::render('programa'))

@section('content')
    @include('alert.alert_json')

    @permission('admin-programas')
    {!! Form::open(['class'=>'form-inline form_noEnter', 'id'=>'form_programa_store']) !!}
    <div class="form-group">
        {!! Form::label('codigo','Código') !!}
        {!! Form::number('codigo',null,['step' => '1','min'=>'1','class'=>'form-control text-uppercase','placeholder'=>'Código']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('programa','Programa') !!}
        {!! Form::text('programa',null,['class'=>'form-control text-uppercase','placeholder'=>'Programa','required']) !!}
    </div>
    {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i> Crear',['class'=>'btn btn-primary tip guardar','data-placement'=>'top', 'title'=>'Crear Programa']) !!}

    {!! Form::close() !!}
    @endpermission
<hr>

<div class="">
    <table class="table" id="programa_table">
        <thead>
        <tr>
            <th>Codigo</th>
            <th>Programa</th>
            <th>Actividades asociadas</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach($programas as $programa)
            <tr>
                <td>{{$programa->cod_programa}}</td>
                <td>{{$programa->programa}}</td>
                <td>
                    @foreach($programa->actividads as $actividad)
                        <b class="text-info">{{$actividad->cod_actividad}}</b> - {{$actividad->actividad}} <br>
                    @endforeach
                </td>
                <td width="10%">
                    @permission('admin-programas')
                    <a href="#!" class="btn btn-sm btn-danger delete tip" data-placement="top" title="Eliminar" data-id="{{$programa->id}}"><i class="fa fa-trash-o"></i></a>
                    <a href="#!" class="btn btn-sm btn-success tip" data-placement="top" title="Editar" data-toggle="modal" data-target="#edit-modal" onclick="mostrarEdit({{$programa->id}})"><i class="fa fa-pencil"></i>
                    </a>
                    <a href="#!" class="btn btn-sm btn-primary tip" data-placement="top" title="Actividades" data-toggle="modal" data-target="#actividades-modal" onclick="loadActividades({{$programa->id}})"> <i class="fa fa-link"></i>
                    </a>
                    @endpermission
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

    @include('programas.edit')
    @include('programas.programa_actividades')

    {{--Borrado en tabla sin refrescar pantalla--}}
    {!! Form::open(['route'=>['admin.programas.destroy','PROG:ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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
        });

        $(document).on('change','#all_actividad',function(event){
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $(document).on('mouseover','.tip',function(event){
            $(this).tooltip();
        });

        $(document).on('click','.guardar',function(event){
            event.preventDefault();
            var data= {
                programa: $("#programa").val(),
                cod_programa: $("#codigo").val()
            };
            $(this).ajaxPost('{{route('admin.programas.store')}}','POST','{{route('admin.programas.index')}}',data);
        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-delete");
            var url=form.attr('action').replace('PROG:ID',id);
            var data=form.serialize();
            swal({
                title: "Confirme para eliminar el programa!",
                text: "Seguro que quiere eliminar el programa?. Esta acción no se podrá deshacer!",
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
                                    swal("Confirmado!", response.message,"success");
                                    console.log(response.message);
                                    row.fadeOut();
                                    },
                                error: function (response) {
                                    row.show();
                                    console.log(response);
                                    swal("ERROR!", response,"error");
                                }
                            });
                        }//isConfirm
                        else {
                            swal("Cancelado", "Canceló la eliminación del programa :)", "error");
                        }
                    });

        });

        //Modal editar
        var mostrarEdit= function(id){
            var url="{{route('admin.programas.edit',':ID')}}";
            var route=url.replace(':ID',id);
            var token = $("input[name=_token]").val();
            $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#programa_id").val(response.id);
                    $("#codigo_edit").val(response.cod_programa);
                    $("#programa_edit").val(response.programa);
                },
                error: function (response) {
                }
            });
        };

        //Modal asociar actividades
        var loadActividades= function(id){ //id=programa_id
            var url="{{route('loadActividades',':ID')}}";
            var route=url.replace(':ID',id);
            var token = $("input[name=_token]").val();
            var act=$("#actividades_list");
            $("#form-vincular").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    act.html(response);
                },
                error: function (response) {
                }
            });
        };

        $(document).on('click','.actualizarProg',function(event){
            var id = $("#programa_id").val();
            var codigo = $("#codigo_edit").val();
            var programa = $("#programa_edit").val();
            var data={cod_programa: codigo, programa:programa};
            var url="{{route('admin.programas.update',':ID')}}";
            var route= url.replace(':ID',id);
            $(this).ajaxPost(route,'PUT','{{route('admin.programas.index')}}',data);
            $("#edit-modal").modal('toggle');
        });

        $(document).on('click','.vincularActividad',function(event){
            var id=$("#pro_id").val();
            var form=$("#form-vincular");
            var data=form.serialize();
            var url="{{route('asociarActividades',':ID')}}";
            var route= url.replace(':ID',id);
            $(this).ajaxPost(route,'post','{{route('admin.programas.index')}}',data);
            $("#actividades-modal").modal('toggle');
        });


        function ajaxRenderSection(url,response) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('.panel-body').empty().append($(data));
                   // cargarDatatable();
                    if (response.estado==='success'){
                        showSucces(response.message);
                    }
                    else {
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
                    //cargarDatatable();
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
                    ajaxRenderSectionError(sectionToRender,response); //errores de validacion
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