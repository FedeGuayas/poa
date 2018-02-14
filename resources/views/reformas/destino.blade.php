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
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Monto para Reforma:
                            <b>$ {!! Form::number('monto_reforma',$reforma->monto_orig,['placeholder'=>'0.00','id'=>'monto_reforma','readonly','style'=>'border: none']) !!}</b>
                        </h4>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="col-lg-5">{!! form::label('por_distribuir','Por distribuir:') !!}
                                <div class="input-group has-success">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                    {!! Form::number('por_distribuir',$reforma->monto_orig,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'por_distribuir','readonly']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <a href="#!" class="btn btn-danger tip" data-placement="top" title="Cancelar Reforma" id="cancelar_reforma"><i class="fa fa-ban" aria-hidden="true"></i> Cancelar la reforma</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover"
                           id="pacs_destino_table" cellspacing="0" style="display: none;">
                        <caption>PACS-Destino</caption>
                        <thead style="background-color: rgba(13, 151, 53, 0.2);">
                        <th style="width: 100px;">Cod_item</th>
                        <th>Item</th>
                        <th style="width: 70px;">Dirección</th>
                        <th style="width: 70px;">Mes</th>
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
                            <th class="search-filter">filtrar</th>
                            <th class="search-filter">filtrar</th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th class="search-filter">filtrar</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach($pacs_All as $pac_destino)
                            {{--@if ($pac_destino->area_item_id != )--}}
                                {{--@continue--}}
                            {{--@endif--}}

                            <tr>
                                <td>{{$pac_destino->cod_programa.'-'.$pac_destino->cod_actividad.'-'.$pac_destino->cod_item}}</td>
                                <td>{{$pac_destino->item}}</td>
                                <td>{{$pac_destino->area}}</td>
                                <td>{{$pac_destino->mes}}</td>
                                <td>{{$pac_destino->concepto}}</td>
                                <td>{{$pac_destino->nombres}} {{$pac_destino->apellidos}}</td>
                                <td class="presupuesto">$ <input type="number" readonly
                                                                 value="{{$pac_destino->presupuesto}}"
                                                                 style="border: none; width: 100px"></td>
                                <td>$ {{$pac_destino->comprometido}}</td>
                                <td>$ {{$pac_destino->devengado}}</td>
                                <td>$ {{$pac_destino->disponible}}</td>
                                <td>
                                    {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>',['class'=>'btn btn-xs btn-success tip agregar_destino','data-placement'=>'top', 'title'=>'Agregar','data-id'=>"{$pac_destino->id}"]) !!}
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>


                {{--Tabla para crear el monto total solicitado en la reforma--}}
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <table id="destino" class="table table-striped table-condensed table-bordered table-hover">
                            <thead style="background-color: rgba(13, 151, 53, 0.2);">
                            <th style="width: 5px;">Accion</th>
                            <th>Item</th>
                            <th style="width: 250px;">Valor</th>
                            </thead>
                            <tfoot>
                            <th>Total</th>
                            <th></th>
                            {{--<th><b><h5 id="total_origen">$ 0.00</h5></b></th>--}}
                            <th>
                                ${!! Form::number('total_destino',null,['placeholder'=>'0.00','id'=>'total_destino','readonly','style'=>'border: none; width:95%;']) !!}
                            </th>
                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--Guardar valores en tabla reformas y pac origen--}}
                <div class="col-md-6" id="enviar_destino" hidden>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',['class'=>'btn btn-sm btn-success tip','data-placement'=>'top', 'title'=>'Guardar destino', 'id'=>'guardar_destino']) !!}
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
    {!! Form::close() !!}

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

        $(document).on('click', '#cancelar_reforma', function (e) {
            e.preventDefault();
            var id = $("#reforma_id").val();
            var form = $("#reforma-delete");
            var url = form.attr('action').replace(':ID', id);
            var data = form.serialize();
           // var redirect="{{route('admin.pacs.index')}}";
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
                            swal("", response.message, "success");
                            $(".sa-confirm-button-container .confirm").on('click', function () {
                                window.setTimeout(function () {
//                                    location.reload()
                                    location.href="{{route('admin.pacs.index')}}";
                                }, 1)});
                        },
                        error: function (response) {
                            swal("ERROR!", response, "error");
                        }
                    });
                }// .end if isConfirm
                else {
                    swal("Cancelado", "Canceló la eliminación de la reforma :)", "error");
                }
            });// .end if isConfirm
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
            var id = $(this).attr('data-id');//id pac
            var row = $(this).parents('tr');
            var item = row.find("td").eq(1).html();
            var max = parseFloat($("#por_distribuir").val());
            swal({
                        title: "Monto!",
                        text: "Valor para agregar a reforma:",
                        type: "input",
                        inputType: "number",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: max
                    },
                    function (inputValue) {

                        if (inputValue > max) {
                            swal.showInputError("El valor entrado es superior al disponible para la reforma!");
                            return false;
                        }
                        if (inputValue === "") {
                            swal.showInputError("Necestita escribir un valor!");
                            return false;
                        }

                        agregar_reforma_destino(inputValue, row, id, item);

                    });
        });

        var contd = 0, totd = 0, subtotald = [];
        function agregar_reforma_destino(inputValue, row, id, item) {
            if (inputValue == '') {
                return false;
            }
            var pac_idd = id;
            var monto_max = parseFloat($("#monto_reforma").val());
            var por_distribuir = parseFloat($("#por_distribuir").val());
            var destino = $("#destino");

            subtotald[contd] = parseFloat(inputValue);
            totd = totd + subtotald[contd];
            if (totd <= monto_max) {

                var filad = '<tr class="selecte" id="filad' + contd + '"><td><button data-contador="' + contd + '" class="btn btn-xs btn-danger eliminard" title="Eliminar"><i class="fa fa-trash-o" aria-hidden="true"></i><input type="hidden" name="pac_idd[]" value="' + pac_idd + '"></button></td><td>' + item + '</td><td style="color: #5cb85c"><input type="hidden" name="subtotal_idd[]" value="' + subtotald[contd] + '"><b>$ ' + subtotald[contd].toFixed(2) + '</b></td></tr>';
                destino.append(filad);
                $("#total_destino").val(totd.toFixed(2));
                contd++;
                evaluar_enviar_destino();
                $("#por_distribuir").val(monto_max - totd);
                swal("Destino!", "Valor agregado : $" + inputValue, "success");
                row.find('.agregar_destino').prop('disabled', true);

            } else {
                totd = totd - subtotald[contd];
                $("#total_destino").val(totd.toFixed(2));
                swal("Error! :(", "No puede superar el monto disponible!", "error")
            }
        }

        $(document).on('click', '.eliminard', function (event) {
            event.preventDefault();
            var index = parseInt($(this).attr('data-contador'));
            var monto_max = parseFloat($("#monto_reforma").val());
            totd = totd - subtotald[index];
            $("#total_destino").val(totd.toFixed(2));
            $("#filad" + index).remove();
            $("#por_distribuir").val(monto_max - totd);
            evaluar_enviar_destino();
//            $(this).closest('tr').remove();
        });

        function evaluar_enviar_destino() {
            if (totd > 0) {
                $("#enviar_destino").show();
            } else {
                $("#enviar_destino").hide();
                $(".agregar_destino").prop('disabled', false);
            }
        }


        //guardar la informacion del origen de la reforma
        $(document).on('click', '#guardar_destino', function (event) {
            event.preventDefault();
            var route = "{{route('admin.reformas.store.destino')}}";
            var form = $("#form-enviar_destino");
            var data = form.serialize();
            $(this).prop('disabled', true);
            $.ajax({
                url: route,
                type: "POST",
                data: data,
                success: function (response) {
                    if (response.tipo === 'error') {
                        swal("ERROR!", response.message, "error");
                        $(".agregar_destino").prop('disabled', false);
                        $("#guardar_destino").prop('disabled', false);
                    } else if (response.tipo === 'error_critico') { //este error elimina la reforma creada
                        swal("ERROR!", response.message, "error");
                        $("#recargar_destino").show();
                    } else { //ok, sin errores
//                        swal("!",response.message,"success");
                        swal({
                                    title: response.message,
//                                    text: "Seguro que quiere eliminar el programa?. Esta acción no se podrá deshacer!",
                                    type: "success",
                                    showCancelButton: false,
//                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "OK!",
//                                    cancelButtonText: " NO!",
                                    closeOnConfirm: true,
//                                    closeOnCancel: false,
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

                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });


    </script>
@endsection