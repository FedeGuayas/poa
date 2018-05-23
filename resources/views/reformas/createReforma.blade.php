@extends('layouts.master')
@section('title','Reformas')

@section('breadcrumbs', Breadcrumbs::render('reformas-solicitud'))

@section('content')

    <div class="col-md-12">
        <div class="row">
            <div class="col-sm-6">
                @include('alert.alert')
                @include('alert.request')
                @include('alert.alert_json')
            </div>
        </div>

        {{--POA ORIGEN PARA REFORMA--}}
        <div class="panel panel-warning" id="panel_poa_origen">
            <div class="panel-heading clearfix">POA-ORIGEN
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa_origen"
                   aria-expanded="false" aria-controls="poa_origen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="poa_origen">
                {!! Form::open(['route'=>'admin.reformas.store','method'=>'post','id'=>'form-enviar_origen']) !!}
                {!! Form::hidden('area_item_origen',$codigos->id,['id'=>'area_item_origen']) !!}
                {!! Form::hidden('reform_type_id',$reform_type_id,['id'=>'reform_type_id']) !!}

                @if ($tipo_reforma!='INTERNA')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <i class="fa fa-pencil"></i>
                                {!! Form::label('informe','Justificativo origen:') !!}
                                {!! Form::textarea('informe',null,['class'=>'form-control','placeholder'=>'Justificación origen para el informe técnico...','rows'=>'2','autofocus','required']) !!}
                                <small id="notaHelpBlock" class="form-text text-muted">
                                    Ejemplo Justificativo origen: Se cancelaron los viajes planificados para el mes de enero ...
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-1 has-success">Programa:
                            <input type="text" class="form-control input-sm" disabled value="{{$codigos->cod_programa}}"
                                   style="width: 100%; text-align: center" id="cod_programa_origen">
                        </div>
                        <div class="col-lg-1 has-success">Actividad:
                            <input type="text" class="form-control input-sm" disabled value="{{$codigos->cod_actividad}}"
                                   style="width: 100%; text-align: center" id="cod_actividad_origen">
                        </div>
                        <div class="col-lg-1 has-success">Cod_Item:
                            <input type="text" class="form-control input-sm" disabled value="{{$codigos->cod_item}}"
                                   style="width: 100%; text-align: center" id="cod_item_orig">
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-5 has-success">Item:
                        <input type="text" class="form-control input-sm" disabled value="{{$codigos->item}}"
                               style="width: 100%; text-align: center" id="cod_item">
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">Disponible:
                            <div class="input-group has-warning">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {{--liberado--}}
                                {!! Form::number('disponible',$poa_disponible,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'disponible','readonly']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2 has-error">
                            {!! Form::number('resto',null,['class'=>'form-control input-sm tip','data-placement'=>'top','title'=>'Por distribuir','placeholder'=>'0.00','id'=>'resto','readonly']) !!}
                        </div>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"
                           cellspacing="0" style="display: none; font-size: 10px;">
                        <caption>PACS-ORIGEN</caption>
                        <thead style="background-color: rgba(236, 198, 77, 0.33);">
                        <th style="width: 100px;">Cod_item</th>
                        <th>Item</th>
                        <th>Dirección</th>
                        <th style="width: 50px;">Mes</th>
                        <th>Concepto</th>
                        <th style="width: auto;">Responsable</th>
                        <th style="width: 100px;">Liberado</th>
                        <th style="width: 5px;">Acción</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @if (count($pacs)>0)
                            @foreach($pacs as $pac)
                                <tr>
                                    <td>{{$pac->cod_programa.'-'.$pac->cod_actividad.'-'.$pac->cod_item}}</td>
                                    <td>{{$pac->item}}</td>
                                    <td>{{$pac->area}}</td>
                                    <td>{{$pac->mes}}</td>
                                    <td>{{$pac->concepto}}</td>
                                    <td>{{$pac->nombres.' '.$pac->apellidos}}</td>
                                    <td class="no_ejecutado">$ <input type="number" readonly value="{{$pac->valor_reformar_origen}}"
                                                                      style="border: none; width: 100px">
                                    </td>
                                    <td>
                                        {!! Form::button('<i class="fa fa-minus" aria-hidden="true"></i>',['class'=>'btn btn-xs btn-warning tip agregar','data-placement'=>'top', 'title'=>'Agregar al destino de la Reforma','data-id'=>"{$pac->id}"]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                {{--Tabla para crear el monto total solicitado en la reforma--}}
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <table id="origen" class="table table-striped table-condensed table-bordered table-hover">
                            <thead style="background-color: rgba(236, 198, 77, 0.33);">
                            <tr>
                                <th style="width: 5px;">Accion</th>
                                <th>Item</th>
                                <th style="width: 250px;">Valor</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <th>Total</th>
                            <th></th>
                            <th>
                                {{--<h4 id="total_origen">$ 0.00</h4>--}}
                                ${!! Form::number('total_origen',null,['placeholder'=>'0.00','id'=>'total_origen','readonly','style'=>'border: none; width:95%;']) !!}
                            </th>
                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--Guardar valores en tabla reformas y pac origen--}}
                <div class="col-md-6" id="enviar_origen" hidden>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar <i class="fa fa-refresh fa-spin fa-2x fa-fw text-danger hidden load_reform_create"></i>',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Guardar Origen', 'id'=>'guardar_origen', 'type'=>'submit']) !!}
                        </div>
                    </div>
                    <div class="col-lg-2">
                        {{--Recargar pagina--}}
                        <div id="recargar" hidden>
                            <div class="form-group">
                                {!! Form::button('<i class="fa fa-refresh" aria-hidden="true"></i> Refrescar',['class'=>'btn btn-sm btn-primary tip recargar_pagina','data-placement'=>'top', 'title'=>'Refrescar página']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>{{--./panel-body--}}
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

@endsection

@section('scripts')
    <script>

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(".selectpicker").selectpicker({
            liveSearch: true,
            liveSearchPlaceholder: 'Buscar'
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
            var table = $("#pac_table").DataTable({
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

            $("#pac_table").fadeIn();


            $('#pac_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
            });

            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });


            $("form").submit('', function (event) {
                var load = $(".load_reform_create");
                load.removeClass('hidden');
                event.preventDefault();
                $("#guardar_origen").prop("disabled", true);
                event.currentTarget.submit();
                load.addClass('hidden');
            });

        });

        // ************   LLENAR TABLA PARA ORIGEN DE REFORMA  **********************//
        $(document).on('click', '.agregar', function (event) {
            var id = $(this).attr('data-id');//id pac tomado del boton
            var row = $(this).parents('tr'); //fila
//            var max = parseFloat(row.find('.no_ejecutado').find('input').val());//max valor del input en la fila con la clase no_ejecutado
            var max = parseFloat(row.find('td>input').val());//max valor del input en la fila con la clase no_ejecutado
            var item = row.find("td").eq(1).html(); //nombe del item
            swal({
                    title: "Monto!",
                    text: "Valor a obtener para reforma:",
                    type: "input",
                    inputType: "number",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: max
                }, function (inputValue) {

                    if (inputValue===false) {//si se da en cancel
                        console.log("cancelo");
                        return false;
                    } else { //acepto
                        if (inputValue > max) {
                            swal.showInputError("No puede tomar un valor superior al disponible !");
                            return false;
                        }
                        if (inputValue === "" || inputValue === "0") {
                            swal.showInputError("Es obligatorio entrar datos!");
                            return false;
                        }
                        agregar_reforma_origen(inputValue, row, id, item);
                    }

                });

        });

        var cont = 0, tot = 0, resto = 0, subtotal = [];
        ids_origen = [];
        function agregar_reforma_origen(inputValue, row, id, item) {

            if (inputValue === '' || inputValue === "0") {
                return false;
            }
            var pac_id = id;
            var valor = parseFloat(inputValue);
            var disponible = parseFloat($("#disponible").val());
            var origen = $("#origen");

            subtotal[cont] = valor;
            tot = Math.round((tot + subtotal[cont]) * 100) / 100;
            if (tot <= disponible) {
                var fila = '<tr class="selected" id="fila' + cont + '"><td><button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminar(' + cont + ');"><i class="fa fa-trash-o" aria-hidden="true"></i><input type="hidden" name="pac_id[]" value="' + pac_id + '"></button></td><td>' + item + '</td><td style="color: #5cb85c"><input type="hidden" name="subtotal_id[]" value="' + subtotal[cont] + '"><b>$ ' + subtotal[cont].toFixed(2) + '</b></td></tr>';
                origen.append(fila);
                evaluar_enviar_origen();
                $("#total_origen").val(tot);
//                $("#total_origen").html("$ " + tot.toFixed(2));
                resto = Math.round((disponible - tot) * 100) / 100;
                $("#resto").val(resto);
                cont++;
                swal("Origen!", "Valor agregado : $" + inputValue, "success");
                row.find('.agregar').prop('disabled', true);
            } else {
                tot = Math.round((tot - subtotal[cont]) * 100) / 100;
                $("#total_origen").val(tot);
                $("#resto").val(resto);
//                $("#total").html("$ " + tot.toFixed(2));
                swal("Error! :(", "No puede superar el monto disponible!", "error")
            }
        }

        function eliminar(index) {
            tot = Math.round((tot - subtotal[index]) * 100) / 100;
            resto = Math.round((resto + subtotal[index]) * 100) / 100;
//            $("#total_origen").html("$ " + tot.toFixed(2));
            $("#resto").val(resto);
            $("#fila" + index).remove();
            $("#total_origen").val(tot);
            evaluar_enviar_origen()
        }
        function evaluar_enviar_origen() {
            if (tot > 0) {
                $("#enviar_origen").show();
            } else {
                $("#enviar_origen").hide();
                $(".agregar").prop('disabled', false);
            }
        }


        $(document).on('click', '.recargar_pagina', function (event) {
            event.preventDefault();
            window.setTimeout(function () {
                location.reload()
            }, 1) //recarga la pagina
        });


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