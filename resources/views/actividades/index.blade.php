@extends('layouts.master')
@section('title','Actividades')
@section('breadcrumbs', Breadcrumbs::render('actividad'))

@section('content')
    @include('alert.alert_json')

    {{--@if(count($actividades)<1)--}}
        {{--<div class="pull-right">--}}
            {{--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">--}}
                {{--<label class="btn btn-block btn-success" for="actividad_file">--}}
                    {{--<i class="fa fa-file-excel-o" aria-hidden="true"></i>--}}
                    {{--Buscar--}}
                    {{--{!! Form::file('actividad_file',['id'=>'actividad_file','style'=>'display:none']) !!}--}}
                {{--</label>--}}
                {{--<span class='label label-info' id="upload-file-info"></span>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">--}}
                {{--<button type="button" id="cargar" class="btn btn-primary btn-block" disabled>--}}
                    {{--Cargar <span class="badge"></span>--}}
                {{--</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}
    @permission('admin-actividades')
    {!! Form::open(['class'=>'form-inline form_noEnter', 'id'=>'form_actividad_store']) !!}
    <div class="form-group">
        {!! Form::label('codigo','Código') !!}
        {!! Form::number('codigo',null,['step' => '1','min'=>'1','class'=>'form-control text-uppercase','placeholder'=>'Código','required']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('actividad','Actividad') !!}
        {!! Form::text('actividad',null,['class'=>'form-control text-uppercase','placeholder'=>'Actividad','required']) !!}
    </div>
    {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i> Crear',['class'=>'btn btn-primary tip guardar','data-placement'=>'top', 'title'=>'Crear Actividad', 'type'=>'submit']) !!}

    {!! Form::close() !!}
    @endpermission
<hr>
<div class="col-lg-12">
    <table class="table" id="actividad_table">
        <thead>
        <tr>
            <th>Codigo</th>
            <th>Actividad</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $actividad)
            <tr>
                <td>{{$actividad->cod_actividad}}</td>
                <td>{{$actividad->actividad}}</td>
                <td>
                    @permission('admin-actividades')
                    <a href="#!" class="btn btn-sm btn-danger delete tip" data-placement="top" title="Eliminar" data-id="{{$actividad->id}}"><i class="fa fa-trash-o"></i></a>
                    <a href="#!" class="btn btn-sm btn-success tip" data-placement="top" title="Editar" data-toggle="modal" data-target="#edit-modal" onclick="mostrarEdit({{$actividad->id}})"><i class="fa fa-pencil"></i>
                    </a>
                    @endpermission
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

    @include('actividades.edit')

    {{--Borrado en tabla sin refrescar pantalla--}}
    {!! Form::open(['route'=>['admin.actividades.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
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

        });

        $(document).on('mouseover','.tip',function(event){
            $(this).tooltip();
        });

        $(document).on('click','.guardar',function(event){
            event.preventDefault();
            var data= {
                actividad: $("#actividad").val(),
                cod_actividad: $("#codigo").val()
            }
            $(this).ajaxPost('{{route('admin.actividades.store')}}','POST','{{route('admin.actividades.index')}}',data);
        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var form=$("#form-delete")
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                title: "Confirme para eliminar la actividad!",
                text: "Seguro que quiere eliminar la actividad?. Esta acción no se podrá deshacer!",
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
                            swal("Cancelado", "Canceló la eliminación de la actividad :)", "error");
                        }
                    });

        });

        //Modal editar
        var mostrarEdit= function(id){
            var url="{{route('admin.actividades.edit',':ID')}}"
            var route=url.replace(':ID',id);
            var token = $("input[name=_token]").val();
            $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#actividad_id").val(response.id);
                    $("#codigo_edit").val(response.cod_actividad);
                    $("#actividad_edit").val(response.actividad);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        };

        $(document).on('click','.actualizarAct',function(event){
            var id = $("#actividad_id").val();
            var codigo = $("#codigo_edit").val();
            var actividad = $("#actividad_edit").val();
            var data={cod_actividad: codigo, actividad:actividad};
            var url="{{route('admin.actividades.update',':ID')}}";
            var route= url.replace(':ID',id);
            $(this).ajaxPost(route,'PUT','{{route('admin.actividades.index')}}',data);
            $("#edit-modal").modal('toggle');
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