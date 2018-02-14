@extends('layouts.master')
@section('title','Reformas')

@section('breadcrumbs', Breadcrumbs::render('reformas-solicitud'))

@section('content')

    <div class="col-md-12">
        <div class="row">
            <div class="col-sm-6">
                @include('alert.alert')
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
                <div class="row">
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
                    <div class="col-lg-6 has-success">Item:
                        <input type="text" class="form-control input-sm" disabled value="{{$codigos->item}}"
                               style="width: 100%; text-align: center" id="cod_item">
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">Disponible:
                            <div class="input-group has-warning">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('disponible',$poa_disponible,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'disponible','readonly']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="10">
                        <div class="col-lg-2">
                            {!! Form::label('tipo','Tipo de Reforma') !!} <span class="text-danger fa-lg">*</span>
                            {!! Form::select('tipo',['INTERNA'=>'INTERNA','INFORMATIVA'=>'INFORMATIVA','MINISTERIAL'=>'MINISTERIAL'],null,['class'=>'form-control','placeholder'=>'Seleccione ...','id'=>'tipo']) !!}
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <i class="fa fa-pencil"></i>
                                {!! Form::label('nota','Observaciones:') !!}
                                {!! Form::textarea('nota',null,['class'=>'form-control','length'=>'255','style'=>'text-transform:uppercase','placeholder'=>'Observaciones...','rows'=>'3', 'cols'=>'50']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"
                           cellspacing="0" style="display: none;">
                        <caption>PACS-ORIGEN</caption>
                        <thead style="background-color: rgba(236, 198, 77, 0.33);">
                        <th style="width: 100px;">Cod_item</th>
                        <th>Item</th>
                        <th>Dirección</th>
                        <th style="width: 50px;">Mes</th>
                        <th>Concepto</th>
                        <th style="width: auto;">Responsable</th>
                        <th style="width: 100px;">Presupuesto</th>
                        <th style="width: 100px;">Ejecutado</th>
                        <th style="width: 100px;">Devengado</th>
                        <th style="width: 100px;">Disponible</th>
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
                                <td>$ {{$pac->presupuesto}}</td>
                                <td>$ {{$pac->comprometido}} </td>
                                <td>$ {{$pac->devengado}}</td>
                                <td class="no_ejecutado">$ <input type="number" readonly value="{{$pac->disponible}}"
                                                                  style="border: none; width: 100px"></td>
                                <td>
                                    {!! Form::button('<i class="fa fa-minus" aria-hidden="true"></i>',['class'=>'btn btn-xs btn-warning tip agregar','data-placement'=>'top', 'title'=>'Agregar al destino Reforma','data-id'=>"{$pac->id}"]) !!}
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
                            <th style="width: 5px;">Accion</th>
                            <th>Item</th>
                            <th style="width: 250px;">Valor</th>
                            </thead>
                            <tfoot>
                            <th>Total</th>
                            <th></th>
                            {{--<th><b><h5 id="total_origen">$ 0.00</h5></b></th>--}}
                            <th>
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
                            {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Guardar Origen', 'id'=>'guardar_origen', 'type'=>'submit']) !!}
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


        });

        // ************   LLENAR TABLA PARA ORIGEN DE REFORMA  **********************//
        $(document).on('click', '.agregar', function (event) {
            var id = $(this).attr('data-id');//id pac
            var row = $(this).parents('tr');
            var max = parseFloat(row.find('.no_ejecutado').find('input').val());
            var item = row.find("td").eq(1).html();
            var tipo = $("#tipo").val();
            if  (tipo ==""){
                swal("","Debe seleccionar el tipo de reforma","error");
            }else{
                swal({
                            title: "Monto!",
                            text: "Valor a obtener para reforma:",
                            type: "input",
                            inputType: "number",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            animation: "slide-from-top",
                            inputPlaceholder: max
                        },
                        function (inputValue) {

                            if (inputValue > max) {
                                swal.showInputError("No puede restar un valor superior al disponible !");
                                return false;
                            }
                            if (inputValue === "") {
                                swal.showInputError("Necestita escribir algo!");
                                return false;
                            }

                            agregar_reforma_origen(inputValue, row, id, item);

                        });
            }

        });

        var cont = 0, tot = 0, subtotal = []; ids_origen = [];
        function agregar_reforma_origen(inputValue, row, id, item) {
            if (inputValue === '') {
                return false;
            }
            var pac_id = id;
            var disponible = parseFloat($("#disponible").val());
            var origen = $("#origen");
            subtotal[cont] = parseFloat(inputValue);
            tot = tot + subtotal[cont];
            if (tot <= disponible) {
                var fila = '<tr class="selected" id="fila' + cont + '"><td><button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminar(' + cont + ');"><i class="fa fa-trash-o" aria-hidden="true"></i><input type="hidden" name="pac_id[]" value="' + pac_id + '"></button></td><td>' + item + '</td><td style="color: #5cb85c"><input type="hidden" name="subtotal_id[]" value="' + subtotal[cont] + '"><b>$ ' + subtotal[cont].toFixed(2) + '</b></td></tr>';
                origen.append(fila);
                $("#total_origen").val(tot.toFixed(2));
                cont++;
                evaluar_enviar_origen();
                swal("Origen!", "Valor agregado : $" + inputValue, "success");
                row.find('.agregar').prop('disabled', true);
            } else {
                tot = tot - subtotal[cont];
                $("#total_origen").val(tot.toFixed(2));
                swal("Error! :(", "No puede superar el monto disponible!", "error")
            }
        }
        function eliminar(index) {
            tot = tot - subtotal[index];
            $("#total_origen").val(tot.toFixed(2));
            $("#fila" + index).remove();
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