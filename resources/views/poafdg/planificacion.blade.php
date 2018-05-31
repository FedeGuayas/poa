@extends('layouts.master')
@section('title','POA-Planificación')

@section('breadcrumbs', Breadcrumbs::render('poa-fdg'))

@section('content')
    @include('alert.alert')
    @include('alert.alert_json')

    {!! Form::open(['route'=>'storePlanificacion','method'=>'post', 'id'=>'form-planificacion-store']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading clearfix">PLANIFICACION
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa-fdg"
                       aria-expanded="false" aria-controls="poa-fdg"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="poa-fdg">

                    <div class="row">
                        <div class="form-group">
                            <div class="form-group">
                                <div class="col-lg-4"><span class="text-danger fa-lg">*</span>
                                    {!! Form::select('programa',$list_programs,null,['class'=>'form-control selectpicker','placeholder'=>'Seleccione programa...','id'=>'programa']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4"><span class="text-danger fa-lg">*</span> <i class="fa fa-spinner fa-pulse fa-fw text-primary hidden load_act"></i>
                                    {!! Form::select('actividad',[],null,['class'=>'form-control selectpicker','placeholder'=>'Seleccione actividad...','id'=>'actividad']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4"><span class="text-danger fa-lg">*</span> <i class="fa fa-spinner fa-pulse fa-fw text-primary hidden load_item"></i>
                                    {!! Form::select('item',[],null,['class'=>'form-control selectpicker','placeholder'=>'Seleccione item...','id'=>'item']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-3">CODIGO:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-hashtag text-info"></i></span>
                                <strong><input type="text" class="form-control input-sm" disabled value=""
                                               style="width: 100%; text-align: center" id="cod_item"></strong>
                            </div>
                        </div>
                        <div class="col-lg-3">PRESUPUESTO:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-dollar text-info"></i></span>
                                <strong><input type="text" class="form-control input-sm" disabled value=""
                                               style="width: 100%; text-align: center" id="presupuesto_item"></strong>
                            </div>
                        </div>
                        <div class="col-lg-3">DISPONIBLE:
                            <div class="input-group has-success">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('disponible',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'disponible','readonly']) !!}
                            </div>
                        </div>
                    </div>

                </div>{{--./panel-collapse--}}
            </div>{{--./panel-info--}}
        </div>{{--./col-md-12--}}


        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading clearfix">PLANIFICACION
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#programar"
                       aria-expanded="false" aria-controls="programar"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="programar">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                {{--{!! Form::label('area','',['class'=>'sr-only']) !!}--}}
                                {!! Form::select('area',$list_areas,null,['class'=>'form-control selectpicker','placeholder'=>'Direcciones...','id'=>'area']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('mes','',['class'=>'sr-only']) !!}
                            {!! Form::select('mes',$list_meses,$mes,['class'=>'form-control selectpicker','placeholder'=>'Meses...','id'=>'mes']) !!}
                        </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                            <div class="input-group has-warning small">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('valor',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'Valor','placeholder'=>'0.00','id'=>'valor']) !!}
                                <span class="input-group-addon">.00</span>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                        {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>',['class'=>'btn btn-warning tip','data-placement'=>'top', 'title'=>'Agregar','id'=>'agregar']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2 pull-right">
                            <div class="form-group">
                                <div class="input-group has-error">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                    {!! Form::number('resto',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'Por distribuir','placeholder'=>'0.00','id'=>'resto','readonly']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <table id="detalles" class="table table-striped table-condensed table-bordered table-hover planificacion">
                                <thead style="background-color: rgba(236, 198, 77, 0.33);">
                                <th>Accion</th>
                                <th>Dirección</th>
                                <th>Mes</th>
                                <th>Valor</th>
                                </thead>
                                <tfoot>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th><h4 id="total">$ 0.00</h4></th>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6" id="enviar" hidden>
                            <div class="form-group">
                                {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Guardar', 'type'=>'submit']) !!}
                                {!! Form::button('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar',['class'=>'btn btn-sm btn-danger tip','data-placement'=>'top', 'type'=>'reset', 'title'=>'Cancelar']) !!}
                            </div>
                        </div>
                    </div>

                </div>{{--./panel-body programar collapse in--}}
            </div>{{--./panel-warning--}}
        </div>{{--./col-md-12--}}
        {!! Form::close() !!}

        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading clearfix">DETALLES POA
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                       aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="resumen">
                    {{--<div style="overflow:auto;">--}}
                        <div id="desglose_item"></div>
                    {{--</div>--}}{{--./style overflow:auto--}}
                </div>
            </div>{{--./panel-success--}}
        </div>{{--./col-md-12--}}
    </div>{{--./row--}}


@endsection

@section('scripts')

    <script>

        //Modal editar
        var mostrarEdit= function(id){
            var url="{{route('admin.poa.edit',':ID')}}";
            var route=url.replace(':ID',id);
            var token = $("input[name=_token]").val();
            $("#form-poa-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    $("#item_edit_id").val(response.id);
                    $("#monto_edit").val(response.monto);
                },
                error: function (response) {
                }
            });
        };

        $(document).on('click','.actualizar',function(e){
            var token = $("input[name=_token]").val();
            var id = $("#item_edit_id").val();
            var monto =  parseFloat($("#monto_edit").val());
            var item = $("#item");
            var area = $("#area");
            var url="{{route('admin.poa.update',':ID')}}";
            var route= url.replace(':ID',id);
            var data={monto: monto};
            $("#edit-plan").modal('toggle');
            $.ajax({
                url:route,
                data:data,
                type: 'PUT',
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    item.val("option:eq(0)").prop('selected', true);
                    area.val("option:eq(0)").prop('selected', true);
                    item.selectpicker("refresh");
                    area.selectpicker("refresh");
                    if (response.tipo==='error'){
                        swal("", response.message,"error");
                    } else {
                        swal("", response.message,"success");
                    }
                },
                error: function (response) {
                    var errors = response.responseJSON;
                    var error='';
                    if (errors) {
                        $.each(errors, function (i) {
                            error += errors[i] + '<br>';
                        });
                        swal("Error!", response.message,"success");
                        //showError(error);
                    }
                }
            });
        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var row=$(this).parents('tr');
            var id=$(this).attr('data-id');
            var item = $("#item");
            var area = $("#area");
            var form = $("#form-delete");
            var url=form.attr('action').replace(':ID',id);
            var data=form.serialize();
            swal({
                        title: "Confirme para eliminar !",
                        text: "Se eliminará el monto planificado. Esta acción no se podrá deshacer!",
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
                                    if (response.tipo==='error'){
                                        swal("", response.message,"error");
                                    } else {
                                        swal("Confirmado!", response.message,"success");
                                        row.fadeOut();
                                        item.val("option:eq(0)").prop('selected', true);
                                        area.val("option:eq(0)").prop('selected', true);
                                        item.selectpicker("refresh");
                                        area.selectpicker("refresh");
                                    }

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

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(".selectpicker").selectpicker({
            liveSearch: true,
            liveSearchPlaceholder: 'Buscar'
        });

        $(document).ready(function () {

            $("#programa").change(function () {
                var id = this.value;
                var token = $("input[name=_token]").val();
                var route = "{{route('poaFDG')}}";
                var act = $("#actividad");
                var item = $("#item");
                var load=$(".load_act");
                load.removeClass('hidden');
                $("#cod_item").val('');
                $("#presupuesto_item").val('');
                $("#disponible").val('');
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
                        item.find("option:gt(0)").remove();
                        for (i = 0; i < response.length; i++) {
                            act.append('<option value="' + response[i].id + '">' + response[i].cod_actividad + ' - ' + response[i].actividad + '</option>');
                        }
                        act.selectpicker("refresh");
                        item.selectpicker("refresh");
                        load.addClass('hidden');
                    },
                    error: function (response) {
                        load.addClass('hidden');
                        act.find("option:gt(0)").remove();
                        item.find("option:gt(0)").remove();
                        act.selectpicker("refresh");
                        item.selectpicker("refresh");
                    }
                });
            });

            $("#actividad").change(function () {
                var id = this.value;
                var prog_id = $("#programa").val();
                var token = $("input[name=_token]").val();
                var route = "{{route('getItem')}}";
                var item = $("#item");
                var load=$(".load_item");
                load.removeClass('hidden');
                $("#cod_item").val('');
                $("#presupuesto_item").val('');
                $("#disponible").val('');
                var data = {
                    prog_id: prog_id,
                    act_id: id
                };
                $.ajax({
                    url: route,
                    type: "GET",
                    headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        item.find("option:gt(0)").remove();
                        for (i = 0; i < response.length; i++) {
                            item.append('<option value="' + response[i].id + '">' + response[i].cod_item + ' - ' + response[i].item + '</option>');
                        }
                        item.selectpicker("refresh");
                        load.addClass('hidden');
                    },
                    error: function (response) {
                        load.addClass('hidden');
                        item.find("option:gt(0)").remove();
                        item.selectpicker("refresh");
                    }
                });
            });

            $("#item").change(function () {
                var id = this.value;
                var token = $("input[name=_token]").val();
                var route = "{{route('getUniqueItem')}}";
                var cod_item = $("#cod_item");
                var presupuesto = $("#presupuesto_item");
                var disponible = $("#disponible");
                var data = {
                    item_id: id
                };
                $.ajax({
                    url: route,
                    type: "GET",
                    headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        cod_item.val(response.cod_item);
                        presupuesto.val(response.presupuesto);
                        disponible.val(response.disponible);
                        //for (i = 0; i < response.length; i++) {
                        //  item.append('<option value="' + response[i].id + '">' + response[i].item + '</option>');
                        //}
                    },
                    error: function (response) {
                    }
                });
            });

            $("#area").change(function () {
                var id = this.value;
                var token = $("input[name=_token]").val();
                var route = "{{route('loadItemArea')}}";
                var item_id = $("#item").val();
                var desglose_item = $("#desglose_item");
                var data = {
                    area_id: id,
                    item_id:item_id
                };
                $.ajax({
                    url: route,
                    type: "GET",
                    headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
//                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        desglose_item.html(response);
                    },
                    error: function (response) {
                    }
                });
            });

        });

        $(document).on('click', '.panel-heading .btn-collapse', function (e) {
            var $this = $(this);
            if (!$this.hasClass('panel-collapsed')) {
                $this.addClass('panel-collapsed');
                $this.find('i').removeClass('fa-minus').addClass('fa-plus');
            } else {
                $this.removeClass('panel-collapsed');
                $this.find('i').removeClass('fa-plus').addClass('fa-minus');
            }
        });

        $(document).ready(function () {
            $("#agregar").click(function () {
                agregar();
            })
        });
        var cont = 0, tot = 0, resto = 0, subtotal = [];
        function agregar() {
            var item_id = $("#item").val();
            var area_id = $("#area").val();
            var area = $("#area option:selected").text();
            var mes = $("#mes").val();
            var mes_mes = $("#mes option:selected").text();
            var valor = parseFloat($("#valor").val());
            var disponible = parseFloat($("#disponible").val());
            var detalles = $("#detalles");

            if (area_id != '' && mes != '' && item_id != '' && valor > 0) {
                subtotal[cont] = valor;
                tot = Math.round((tot + subtotal[cont])*100)/100;
                if (tot <= disponible) {
                    var fila = '<tr class="selected" id="fila' + cont + '"><td><button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminar(' + cont + ');"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td><td><input type="hidden" name="area_id[]" value="' + area_id + '">' + area + '</td><td><input type="hidden" name="mes[]" value="' + mes + '">' + mes_mes + '</td><td style="color: #5cb85c"><input type="hidden" name="subtotal_id[]" value="' + subtotal[cont] + '"><b>$ ' + subtotal[cont].toFixed(2) + '</b></td></tr>';
                    detalles.append(fila);
                    evaluar();
                    limpiar();
                    $("#total").html("$ " + tot.toFixed(2));
                    resto = Math.round((disponible - tot)*100)/100;
                    $("#resto").val(resto);
                    cont++;
                } else {
                    tot = Math.round((tot - subtotal[cont])*100)/100;
                    $("#resto").val(resto);
                    $("#total").html("$ " + tot.toFixed(2));
                    swal("Error! :(", "No puede superar el monto disponible!", "error")
                }
            } else {
                swal("Error! :(", "Complete los datos del formulario!", "error")
            }
        }
        function limpiar() {
            $("#valor").val('');
            $("#mes").val(find("option:gt(0)")).selectpicker("refresh").focus();
        }
        function evaluar() {
            if (tot > 0) {
                $("#enviar").show();
            } else {
                $("#enviar").hide();
            }
        }
        function eliminar(index) {
            tot = Math.round((tot - subtotal[index])*100)/100 ;
            resto = Math.round((resto + subtotal[index])*100)/100 ;
            $("#total").html("$ " + tot.toFixed(2));
            $("#resto").val(resto);
            $("#fila" + index).remove();
            evaluar();
        }

    </script>
@endsection