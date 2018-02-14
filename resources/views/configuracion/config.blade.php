@extends('layouts.master')
@section('title','Apertura')
@section('breadcrumbs', Breadcrumbs::render('incio'))

@section('content')

    <div class="row">
        {!! Form::open(['route'=>'configurationPost','method'=>'POST','class'=>'form-horizontal']) !!}
        <div class="form-group">
            {!! Form::label('year','Año',['class'=>'col-sm-2 control-label']) !!}
            <div class="col-xs-12 col-sm-2 col-md-2">
                {!! Form::number('year',null,['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('iva','IVA',['class'=>'col-sm-2 control-label']) !!}
            <div class="col-xs-12 col-sm-2 col-md-2">
                {!! Form::number('iva',null,['class'=>'form-control','aria-describedby'=>'helpBlock']) !!}
                <span id="iva" class="help-block">Iva en porciento Ej: 14</span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection


@section('scripts')

    <script>
        $(document).ready(function () {

            $("#pac_file").on('change', function () {
                $('#upload-file-info').html($(this).val()).show();
//                $('#cargar').prop('disabled', false);
            });

//            $("#cargar").on('click', function () {
//                cargar();
//            });

            $('[data-toggle="tooltip"]').tooltip();

        });

        {{--function cargar() {--}}
            {{--var token = $("input[name=_token]").val();--}}
            {{--var formData = new FormData(document.getElementById("form_carga"));--}}
            {{--var route = "{{route('importPAC')}}";--}}
            {{--swal({--}}
                {{--title: "Confirme la carga ",--}}
                {{--text: "Se importara el archivo al presente ejercicio!",--}}
                {{--type: "warning",--}}
                {{--showCancelButton: true,--}}
                {{--confirmButtonColor: "#DD6B55",--}}
                {{--confirmButtonText: "SI!",--}}
                {{--cancelButtonText: " NO!",--}}
                {{--closeOnConfirm: false,--}}
                {{--closeOnCancel: false,--}}
                {{--showLoaderOnConfirm: true,--}}
            {{--}, function (isConfirm) {--}}
                {{--if (isConfirm) {--}}
                    {{--setTimeout(function () {--}}
                        {{--$.ajax({--}}
                            {{--url: route,--}}
                            {{--type: "POST",--}}
                            {{--headers: {'X-CSRF-TOKEN': token},--}}
{{--//                            contentType: 'application/x-www-form-urlencoded',--}}
                            {{--data: formData,--}}
                            {{--cache: false,--}}
                            {{--contentType: false,--}}
                            {{--processData: false,--}}
                            {{--success: function (response) {--}}

{{--//                                if (response.tipo == "error") {--}}
{{--//                                    swal("Error", response.response, "error");--}}
{{--//                                } else swal("", response.response, "success");--}}
                            {{--},--}}
                            {{--error: function (response) {--}}
{{--//                                console.log(response.responseJSON);--}}
{{--////                                var errorsHtml='<div class="alert alert-danger"><ul>';--}}
{{--//                                var errors = [];--}}
{{--//                                $.each(response.responseJSON.poa_file, function (key, value) {--}}
{{--////                                    errorsHtml += '<li>' + value[0] + '</li>';--}}
{{--//                                    errors.push(value);--}}
{{--////                                    errors +=  '"'+elem+'"' +'\n';--}}
{{--//                                });--}}
{{--////                                errorsHtml += '</ul></di>';--}}
{{--//                                swal("Error!", errors.toString(), "error");--}}
                            {{--}--}}
                        {{--});--}}
                    {{--}, 2000);--}}
                    {{--$(".sa-confirm-button-container .confirm").on('click', function () {--}}
                        {{--window.setTimeout(function () {--}}
                            {{--location.reload()--}}
                        {{--}, 1)--}}
                    {{--});--}}
                {{--}//isConfirm--}}
                {{--else {--}}
                    {{--swal("Cancelado", "Canceló la carga", "error");--}}
{{--//                    $('#upload-file-info').hide();--}}
{{--//                    $('#cargar').prop('disabled', true);--}}
                {{--}--}}
            {{--});--}}
        {{--}--}}


    </script>
@endsection
