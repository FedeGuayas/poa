@extends('layouts.master')
@section('title','POA-ESIGEF')
@section('breadcrumbs', Breadcrumbs::render('apertura'))

@section('content')

    {!! Form::open(['route'=>'importPOA','method'=>'POST','class'=>'form-horizontal', 'files'=>true, 'id'=>'form_carga']) !!}

    <div class="container-fluid">
        @if (count($ejercicio)>0 )
            <div class="row">

                <div class="col-lg-4">
                    @if ( !empty($ejercicio->ejercicio))
                    <h4>Ejercicio cargado: <a href="#poa_modal"  data-toggle="modal" type="button" class="btn btn-info tip"
                           data-placement="top" title="Ver"><i class="fa fa-eye"></i> {{$ejercicio->ejercicio}}
                        </a>
                    </h4>
                    @else
                        <h4>De click en el boton de reinicio </h4>
                    @endif
                </div>

                <div class="material-switch col-lg-2 tip" data-placement="left" title="Reiniciar">
                    {!! Form::checkbox('reset_ejercicio',null,false,['id'=>'reset_ejercicio']) !!}
                    <label for="reset_ejercicio" class="label-danger"></label>
                </div>
            </div>
        @endif

        @if ($cargar)
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="btn btn-success" for="poa_file">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    Buscar
                    {!! Form::file('poa_file',['accept'=>'.csv','id'=>'poa_file','style'=>'display:none']) !!}
{{--                    {!! Form::file('poa_file',['accept'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','id'=>'poa_file','style'=>'display:none']) !!}--}}
                    {{--{!! Form::file('poa_file',['id'=>'poa_file','style'=>'display:none']) !!}--}}
                </label>
                <span class='label label-info' id="upload-file-info"></span>
            </div>
            {{--<div class="clearfix"></div>--}}
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <button type="button" id="cargar" class="btn btn-primary" disabled>
                    Cargar <span class="badge">{{$year}}</span>
                </button>
            </div>

        @endif

    </div>
    {!! Form::close() !!}
    @include('configuracion.poa_modal')

@endsection

@section('scripts')

    <script>

        $(document).ready(function(){
           var table=$("#poa").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
               searching: true,
               select:true,
                processing: true,
                serverSide: true,
                ajax: "{{route('admin.apertura.index')}}",
                columns:[
                    {data: 'ejercicio'},
                    {data: 'programa'},
                    {data: 'actividad'},
                    {data: 'renglon'},
                    {data: 'codificado'},
                    {data: 'compromiso'},
                    {data: 'devengado'},
                    {data: 'pagado'},
                    {data: 'disponible'}
                ],
                "language": {
                    "decimal": ".",
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
                }
           });

        });



//        dataTable.columns.adjust().responsive.recalc();

        function cargar() {
            var token = $("input[name=_token]").val();
            var formData = new FormData(document.getElementById("form_carga"));
            var route ="{{route('importPOA')}}";
            swal({
                title: "Confirme la carga ",
                text: "Se importara el archivo al presente ejercicio!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "SI!",
                cancelButtonText: " NO!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true,
            }, function (isConfirm) {
                if (isConfirm) {
                    setTimeout(function () {
                        $.ajax({
                            url: route,
                            type: "POST",
                            headers: {'X-CSRF-TOKEN': token},
//                            contentType: 'application/x-www-form-urlencoded',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                if (response.tipo == "error") {
                                    swal("Error", response.response, "error");
                                } else swal("", response.response, "success");
                            },
                            error: function (response) {
                                console.log(response.responseJSON);
//                                var errorsHtml='<div class="alert alert-danger"><ul>';
                                var errors=[];
                                $.each(response.responseJSON.poa_file, function (key, value) {
//                                    errorsHtml += '<li>' + value[0] + '</li>';
                                    errors.push(value) ;
//                                    errors +=  '"'+elem+'"' +'\n';
                                });
//                                errorsHtml += '</ul></di>';
                                swal("Error!", errors.toString(), "error");
                            }
                        });
                    }, 2000);
                    $(".sa-confirm-button-container .confirm").on('click', function () {
                        window.setTimeout(function () {location.reload()}, 1)});
                }//isConfirm
                else {
                    swal("Cancelado", "Canceló la carga", "error");
                    $('#upload-file-info').hide();
                    $('#cargar').prop('disabled', true);
                }
            });
        }

        function ver_poa() {
            var token = $("input[name=_token]").val();
            var route ="{{route('admin.apertura.index')}}";
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
//                data: data,
                success: function (response) {
                    console.log(response);
//                    $("#poa").html(response);
                },
                error: function (response) {
                }
            });

        }

        $(document).ready(function () {

            $("#poa_file").on('change', function () {
                $('#upload-file-info').html($(this).val()).show();
                $('#cargar').prop('disabled', false);
            });

            $("#cargar").on('click', function () {
                cargar();
            });

            $("#ver_poa").on('click', function () {
                ver_poa();
            });

            $(".tip").tooltip();

            $("#reset_ejercicio").on('change', function () {
                if ($(this).is(':checked')) {
                    var route = "{{route('resetPOA')}}";
                    var reset = $(this).val();
                    var token = $("input[name=_token]").val();
                    var data = {
                        reset: reset
                    };
//                    console.log("Checkbox " + $(this).prop("id") + " (" + $(this).val() + ") => Seleccionado");
                    swal({
                        title: "Desea reiniciar el ejercicio",
                        text: "Se perdera los datos cargados anteriormente",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "SI!",
                        cancelButtonText: " NO!",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            setTimeout(function () {
                                        $.ajax({
                                            url: route,
                                            type: "POST",
                                            headers: {'X-CSRF-TOKEN': token},
//                                             contentType: 'application/x-www-form-urlencoded',
                                            dataType: 'json',
                                            data: data,
                                            success: function (response) {
                                                if (response.tipo== "error") {
                                                    swal("Error", response.response, "error");
                                                } else swal(":)", response.response, "success");
                                            },
                                            error: function (response) {
//                                                console.log(response);
                                            }
                                        });
                                    }
                                    , 2000);
                            $(".sa-confirm-button-container .confirm").on('click', function () {
                                window.setTimeout(function () {location.reload()}, 1)});
                        }//isConfirm
                        else {
                            $("#reset_ejercicio").prop('checked', false);
                            swal("Cancelado", "Acción cancelada", "error");
                        }
                    });

                } else {
//                    console.log("Checkbox " + $(this).prop("id") + " (" + $(this).val() + ") => Deseleccionado");
                }
            });
        });


    </script>
@endsection