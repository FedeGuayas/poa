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
    </div>

    {{--POA DESTINO PARA REFORMA--}}
    <div class="col-md-12" id="panel_poa_destino">
        <div class="panel panel-success">
            <div class="panel-heading clearfix">POA-DESTINO
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#poa_destino"
                   aria-expanded="false" aria-controls="poa_destino"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="poa_destino">
                {!! Form::open(['id'=>'form-enviar_destino']) !!}
                {!! Form::hidden('reforma_id',null,['id'=>'reforma_id']) !!}
                {!! Form::hidden('tipo_de_reforma',$tipo_reforma,['id'=>'tipo_de_reforma']) !!}
                <div class="row">
                    <div class="col-sm-10">
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-6 col-md-3 has-success">{!! form::label('cod_poa_origen','POA Origen:') !!}
                                <input type="text" class="form-control input-sm" disabled
                                       value="{{$poa->item->cod_programa.'-'.$poa->item->cod_actividad.'-'.$poa->item->cod_item}}"
                                       style="text-align: center" id="cod_poa_origen">
                            </div>
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                            <div class="col-xs-12 col-sm-6 col-md-3">{!! form::label('monto_reforma','A Reformar:') !!}
                                <div class="input-group has-warning">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                    {!! Form::number('monto_reforma',$reforma->monto_orig,['class'=>'form-control input-sm','id'=>'monto_reforma','readonly']) !!}
                                </div>
                            </div>
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                            <div class="col-xs-12 col-sm-6 col-md-3">{!! form::label('por_distribuir','Por distribuir:') !!}
                                <div class="input-group has-error">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-error"></i></span>
                                    {!! Form::number('por_distribuir',$reforma->monto_orig,['class'=>'form-control input-sm tip','data-placement'=>'top','title'=>'A distribuir','id'=>'por_distribuir','readonly','step'=>'0.01','placeholder'=>'0.00']) !!}
                                </div>
                            </div>
                        </div>
                        <a href="#!" class="btn btn-danger tip pull-right" data-placement="top"
                           title="Cancelar Reforma"
                           id="cancelar_reforma"><i class="fa fa-ban" aria-hidden="true"></i> Cancelar</a>

                    </div>
                </div>
                @if(count($pacs_All)>0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover"
                               id="pacs_destino_table" cellspacing="0" style="display: none; font-size: 10px;">
                            <caption>PACS-Destino</caption>
                            <thead style="background-color: rgba(13, 151, 53, 0.2);">
                            <tr>
                                <th style="width: 100px;">Cod_item</th>
                                <th>Item</th>
                                <th style="width: 70px;">Dirección</th>
                                <th style="width: 70px;">Mes</th>
                                <th>Concepto</th>
                                <th style="width: auto;">Responsable</th>
                                <th style="width: 100px;">Presupuesto</th>
                                <th style="width: 100px;">Disponible</th>
                                <th style="width: 100px;">Liberado</th>
                                <th style="width: 5px;">Acción</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <tbody>

                            @foreach($pacs_All as $pac_destino)

                                <tr>
                                    <td>{{$pac_destino->cod_programa.'-'.$pac_destino->cod_actividad.'-'.$pac_destino->cod_item}}</td>
                                    <td>{{$pac_destino->item}}</td>
                                    <td>{{$pac_destino->area}}</td>
                                    <td>{{$pac_destino->mes}}</td>
                                    <td>{{$pac_destino->concepto}}</td>
                                    <td>{{$pac_destino->nombres}} {{$pac_destino->apellidos}}</td>
                                    <td class="presupuesto">$ <input type="number" readonly
                                                                     value="{{$pac_destino->presupuesto}}"
                                                                     style="border: none;"></td>
                                    <td>$ {{$pac_destino->disponible}}</td>
                                    <td>$ {{$pac_destino->liberado}}</td>
                                    <td>
                                        {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>',['class'=>'btn btn-xs btn-success tip agregar_destino','data-placement'=>'top', 'title'=>'Agregar','data-id'=>"{$pac_destino->id}"]) !!}
                                    </td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-danger col-sm-12" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        No se encontraron destinos para esta reforma, favor cancelar la misma y verificar los pasos o
                        contacte con el administrador del sistema
                    </div>
                @endif

                {{--Tabla para crear el monto total solicitado en la reforma--}}
                <div class="row">
                    <div class="col-sm-12 col-lg-10 col-lg-offset-1">
                        <table id="destino" class="table table-striped table-condensed table-bordered table-hover">
                            <thead style="background-color: rgba(13, 151, 53, 0.2);">
                            <tr>
                                <th style="width: 5px;">Accion</th>
                                <th style="width: 80px;">Mes</th>
                                <th style="width: 200px;">Item</th>
                                @if ($tipo_reforma!='INTERNA')
                                    <th>Justificativo</th>
                                @endif
                                <th style="width: 100px;">Valor</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                @if ($tipo_reforma!='INTERNA')
                                    <th></th>
                                @endif
                                {{--<th><b><h5 id="total_destino">$ 0.00</h5></b></th>--}}
                                <th>
                                    ${!! Form::number('total_destino',null,['step'=>'0.01','id'=>'total_destino','readonly','style'=>'border: none; width:95%;']) !!}
                                </th>
                            </tr>

                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--Guardar valores del destino en tabla reformas y pac origen--}}
                <div class="col-md-6" id="enviar_destino" hidden>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar  <i class="fa fa-refresh fa-spin fa-2x fa-fw text-danger hidden load_reform_create"></i>',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Guardar destino', 'id'=>'guardar_destino']) !!}
                        </div>
                    </div>
                    <div class="col-lg-2">
                        {{--Recargar pagina--}}
                        <div id="recargar_destino" hidden>
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

    {!! Form::open(['route'=>['admin.reformas.destroy',':ID'],'method'=>'DELETE','id'=>'reforma-delete']) !!}
    {!! Form::hidden('cancelada',null,['id'=>'cancelada']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')
    <script>

        $(document).on('click', '.recargar_pagina', function (event) {
            event.preventDefault();
            window.setTimeout(function () {
                location.reload()
            }, 1) //recarga la pagina
        });

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

        $(document).on('click', '#cancelar_reforma', function (e) {
            e.preventDefault();
            var id = $("#reforma_id").val();
            var form = $("#reforma-delete");
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
            swal({
                title: "Cancelar!",
                text: "Confirme para cancelar la reforma",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "SI!",
                cancelButtonText: " NO!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: url,
                        data: data,
                        type: 'POST',
                        success: function (response) {
                            swal("Reforma", response.message, "success");
                            $(".sa-confirm-button-container .confirm").on('click', function () {
                                window.setTimeout(function () {
//                                    location.reload()
                                    location.href = "{{route('admin.pacs.index')}}";
                                }, 1)
                            });
                        },
                        error: function (response) {
                            swal("ERROR!", response, "error");
                        }
                    });
                }// .end if isConfirm
                else {
                    swal("Cancelado", "Canceló la eliminación de la reforma :)", "error");
                }
            });// .end function isConfirm
        });

        function showError(errors) {
            $("#msj-error").html(errors);
            $("#message-danger").fadeIn();
        }

        function showSucces(message) {
            $("#msj-ok").html(message);
            $("#message-success").fadeIn();
        }

        //************* DESTINO ***************//
        $(document).ready(function () {
            //tabla de pacs_destino
            var table = $("#pacs_destino_table").DataTable({
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

            $("#pacs_destino_table").fadeIn();

            $('#pacs_destino_table .search-filter').each(function () {
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

        // ************   LLENAR TABLA PARA DESTINO DE REFORMA  **********************//
        $(document).on('click', '.agregar_destino', function (event) {
            var pac_idd = $(this).attr('data-id');//id pac
            var row = $(this).parents('tr');
            var item = row.find("td").eq(1).html();
            var mes = row.find("td").eq(3).html();
            var disponible = parseFloat($("#monto_reforma").val());
            var resto = parseFloat($("#por_distribuir").val());
            var tipo_reforma = $("#tipo_de_reforma").val();


            swal({
                    title: "Monto!",
                    text: "Valor para agregar a reforma:",
                    type: "input",
                    inputType: "number",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: resto
                },
                function (inputValue) {
                    if (inputValue === false) {//si se da en cancel
                        return false;
                    } else { //acepto
                        if (parseFloat(inputValue) > resto) {
                            swal.showInputError("El valor entrado es superior al disponible para la reforma!");
                            return false;
                        }
                        if (inputValue === "" || inputValue === "0") {
                            swal.showInputError("Necestita escribir un valor!");
                            return false;
                        }
                        agregar_reforma_destino(inputValue, row, pac_idd, item, mes, tipo_reforma);
                    }

                });


        });

        var contd = 0, totd = 0, resto = 0, subtotald = [];
        function agregar_reforma_destino(inputValue, row, pac_idd, item, mes, tipo_reforma) {

            var destino = $("#destino");
            var disponible = $("#monto_reforma").val();

            subtotald[contd] = parseFloat(inputValue);
            totd = Math.round((totd + subtotald[contd]) * 100) / 100;

            if (totd <= disponible) {

                if (tipo_reforma==='INTERNA'){ //sin justificativo destino
                    var filad = '<tr class="selecte" id="filad' + contd + '"><td><button data-contador="' + contd + '" class="btn btn-xs btn-danger eliminard" title="Eliminar"><i class="fa fa-trash-o" aria-hidden="true"></i><input type="hidden" name="pac_idd[]" value="' + pac_idd + '"></button></td><td>' + mes + '</td><td>' + item + '</td><td style="color: #5cb85c"><input type="hidden" name="subtotal_idd[]" value="' + subtotald[contd] + '"><b>$ ' + subtotald[contd].toFixed(2) + '</b></td></tr>';
                }else {
                    var filad = '<tr class="selecte" id="filad' + contd + '"><td><button data-contador="' + contd + '" class="btn btn-xs btn-danger eliminard" title="Eliminar"><i class="fa fa-trash-o" aria-hidden="true"></i><input type="hidden" name="pac_idd[]" value="' + pac_idd + '"></button></td><td>' + mes + '</td><td>' + item + '</td><td><textarea name="justificativo_destino[]" style="width: 100%;"></textarea></td><td style="color: #5cb85c"><input type="hidden" name="subtotal_idd[]" value="' + subtotald[contd] + '"><b>$ ' + subtotald[contd].toFixed(2) + '</b></td></tr>';
                }

                destino.append(filad);
                evaluar_enviar_destino();
//                $("#total_destino").html("$ " + totd.toFixed(2));
                $("#total_destino").val(totd);
                resto = Math.round((disponible - totd) * 100) / 100;
                $("#por_distribuir").val(resto);
                contd++;
                swal("Destino!", "Valor agregado : $" + inputValue, "success");
                row.find('.agregar_destino').prop('disabled', true);

            } else {
                totd = Math.round((totd - subtotald[contd]) * 100) / 100;
                $("#por_distribuir").val(resto);
//                $("#total_destino").html("$ " + totd.toFixed(2));
                $("#total_destino").val(totd);
                swal("Error! :(", "No puede superar el monto disponible!", "error")
            }
        }

        $(document).on('click', '.eliminard', function (event) {
            event.preventDefault();
            var index = parseInt($(this).attr('data-contador'));
            totd = Math.round((totd - subtotald[index]) * 100) / 100;
            resto = Math.round((resto + subtotald[index]) * 100) / 100;
//            $("#total_destino").html("$ " + totd.toFixed(2));
            $("#total_destino").val(totd);
            $("#por_distribuir").val(resto);
            $("#filad" + index).remove();
            evaluar_enviar_destino();
        });

        function evaluar_enviar_destino() {
            if (totd > 0) {
                $("#enviar_destino").show();
            } else {
                $("#enviar_destino").hide();
                $(".agregar_destino").prop('disabled', false);
            }
        }

        //guardar la informacion del destino de la reforma
        $(document).on('click', '#guardar_destino', function (event) {
            event.preventDefault();
            var route = "{{route('admin.reformas.store.destino')}}";
            var form = $("#form-enviar_destino");
            var data = form.serialize();
            $(this).prop('disabled', true);
            var load = $(".load_reform_create");
            load.removeClass('hidden');
            $.ajax({
                url: route,
                type: "POST",
                data: data,
                success: function (response) {
                    if (response.tipo === 'error') {
                        swal("ERROR!", response.message, "error");
//                        $("#recargar_destino").show();
                        $(".agregar_destino").prop('disabled', false);
                        $("#guardar_destino").prop('disabled', false);
                        load.addClass('hidden');
                    } else if (response.tipo === 'error_critico') { //este error elimina la reforma creada
                        swal("ERROR!", response.message, "error");
                        load.addClass('hidden');
                    } else if (response.tipo === 'listar_reformas') {//ok, sin errores y se puede redirigir
                        load.addClass('hidden');
                        swal({
                                title: response.message,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "OK!",
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    //RDIRECCIONAR
                                    var url = "{{route('admin.reformas.index')}}";
                                    $(window).attr('location', url);
//                                        window.setTimeout(function () {location.reload()}, 1); //recarga la pagina
                                }//isConfirm
                            });

                    } else if (response.tipo === 'listar_procesos') {//ok, sin errores pero como no tiene permisos de reformas, redirigir a procesos
                        load.addClass('hidden');
                        swal({
                                title: response.message,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "OK!",
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    //RDIRECCIONAR
                                    var url = "{{route('admin.pacs.index')}}";
                                    $(window).attr('location', url);
//                                        window.setTimeout(function () {location.reload()}, 1); //recarga la pagina
                                }//isConfirm
                            });

                    }
                },
                error: function (response) {
                    load.addClass('hidden');
                }
            });
        });


    </script>
@endsection