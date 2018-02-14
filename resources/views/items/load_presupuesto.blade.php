@extends('layouts.master')
@section('title','POA')
@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')

    {!! Form::open(['class'=>'form-horizontal', 'files'=>true, 'id'=>'form_carga']) !!}
    <h4>Importar presupuesto anual</h4>
    <hr>

    <div class="container-fluid">

        @if ($cargar)
            @permission('importa-presupuesto')
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="btn btn-success" for="item_file">
                    Cargar archivo <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    {!! Form::file('item_file',['accept'=>'.xlsx,.xls','id'=>'item_file','style'=>'display:none']) !!}
                    {{--{!! Form::file('item_file',['id'=>'item_file','style'=>'display:none']) !!}--}}
                </label>
                <span class='label label-info' id="upload-file-info"></span>
            </div>
            {{--<div class="clearfix"></div>--}}
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <button type="button" id="cargar" class="btn btn-primary" disabled>
                    Importar <span class="badge"> <i class="fa fa-upload text-success" aria-hidden="true"></i> </span>
                </button>
            </div>
            @endpermission
        @else
            <h4 class="text-danger" align="center">El presupuesto se encuentra cargado</h4>
        @endif


    </div>
    {!! Form::close() !!}

@endsection

@section('scripts')

    <script>

        function cargar() {
            var token = $("input[name=_token]").val();
            var formData = new FormData(document.getElementById("form_carga"));
            var route ="{{route('importPresupuesto')}}";
            swal({
                title: "Confirme la carga ",
                text: "Se importará el archivo del presupuesto anual!",
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
                                var errors=[];
                                $.each(response.responseJSON.item_file, function (key, value) {
                                    errors.push(value) ;
                                });
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

        $(document).ready(function () {

            $("#item_file").on('change', function () {
                $('#upload-file-info').html($(this).val()).show();
                $('#cargar').prop('disabled', false);
            });

            $("#cargar").on('click', function () {
                cargar();
            });

            $("#ver_poa").on('click', function () {
                ver_poa();
            });

            $('[data-toggle="tooltip"]').tooltip();

        });


    </script>
@endsection