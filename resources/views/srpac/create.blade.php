@extends('layouts.plane')
@section('title','SRPAC')


@section('styles')
    <style>
        body {
            padding-top: 5px;
            /*margin-bottom: 160px;*/
        }
    </style>
@endsection

@section('body')

    <div class="container">
        {!! Form::open(['route'=>'admin.srpacs.store','method'=>'post','id'=>'form_srpacs']) !!}
        {!! Form::hidden('pac_origen_id',$pac->id) !!}
        <div class="row">
            <div class="page-header">
                <h1>Solicitud de Reforma PAC
                    <small><span class="label label-default">SRPAC</span></small>
                </h1>
                @include('alert.alert')
                @include('alert.request')
            </div>

            <p class="text-right"> Guayaquil, {{$fecha_actual->day}} de {{$month}} del {{$fecha_actual->year}} </p>

            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">PAC INICIAL
                        <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#pac_inicial"
                           aria-expanded="false" aria-controls="pac_inicial"><i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <div class="panel-body collapse in" id="pac_inicial" style="overflow: auto;">

                        <table class="table table-bordered table-responsive" style="font-size: 9px;">
                            <thead>
                            <tr>
                                <th>PARTIDA PRESUPUESTARIA / CUENTA CONTABLE</th>
                                <th>CÓDIGO / CATEGORÍA CPC</th>
                                <th>TIPO COMPRA</th>
                                <th>DETALLE DEL PRODUCTO</th>
                                <th>PAC INICIAL</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <tr>
                                <td>{{$pac->cod_item}}</td>
                                <td>{{$pac->cpc}}</td>
                                <td>{{$pac->tipo_compra}}</td>
                                <td>{{$pac->concepto}}</td>
                                <td class="tip" data-placement="top" title="Valor sin incluir IVA">
                                    $ {{round((($pac->presupuesto-$pac->comprometido-$pac->devengado)/1.12),2)}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">PAC MODIFICADO
                        <a href="#!" class="btn-collapse pull-right" data-toggle="collapse"
                           data-target="#pac_modificado"
                           aria-expanded="false" aria-controls="pac_modificado"><i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <div class="panel-body collapse in" id="pac_modificado" style="overflow: auto;">

                        <div class="row clearfix">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-responsive" id="tabla_modificado"
                                       style="font-size: 10px;">
                                    <thead>
                                    <tr>
                                        <th width="10px">#</th>
                                        <th width="150px" class="text-center">PARTIDA PRESUPUESTARIA / CUENTA CONTABLE
                                        </th>
                                        <th width="150px" class="text-center">CÓDIGO / CATEGORÍA CPC</th>
                                        <th width="150px" class="text-center">TIPO COMPRA</th>
                                        <th class="text-center">DETALLE DEL PRODUCTO</th>
                                        <th width="150px" class="text-center">PAC MODIFICADO</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr id='row0'>
                                        <td style='vertical-align: middle'><input type="hidden" readonly
                                                                                  name="pac_id_destino[]"
                                                                                  value="{{$pac->id}}">
                                            1
                                        </td>
                                        <td style="vertical-align: middle">
                                            <div class="input-group">
                                                <input type="number" readonly class="form-control input-sm"
                                                       name="cod_item[]" value="{{$pac->cod_item}}"
                                                       placeholder="Partida" style="text-align: center">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default btn-sm tip" type="button"
                                                            data-placement="top" title="Buscar Pac" data-toggle="modal"
                                                            data-target="#searchPAC">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </span>
                                            </div><!-- /input-group -->
                                        </td>
                                        <td style="vertical-align: middle">
                                            <input type="number" name="cpc[]" value="{{$pac->cpc}}" placeholder="CPC"
                                                   style="text-align: center" class="form-control input-sm">
                                        </td>
                                        <td style="vertical-align: middle">
                                            <select name="tipo_compra[]" class="form-control input-sm">
                                                <option {{$pac->tipo_compra=="BIEN"? 'selected':''}} value="BIEN">BIEN
                                                </option>
                                                <option {{$pac->tipo_compra=="OBRA"? 'selected':''}} value="OBRA">OBRA
                                                </option>
                                                <option {{$pac->tipo_compra=="SERVICIO"? 'selected':''}} value="SERVICIO">
                                                    SERVICIO
                                                </option>
                                                <option {{$pac->tipo_compra=="CONSULTORIA"? 'selected':''}} value="CONSULTORIA">
                                                    CONSULTORIA
                                                </option>
                                            </select>
                                        </td>
                                        <td style="vertical-align: middle">
                                            <textarea type="textarea" name="concepto[]" rows="4"
                                                      style="width: 100%;height: 100%; text-transform: uppercase; text-align: justify"
                                                      class="form-control input-sm">{{$pac->concepto}}</textarea>
                                        </td>
                                        <td style="vertical-align: middle">
                                            <input type="number" name="presupuesto[]" step="0.01"
                                                   value="{{round((($pac->presupuesto-$pac->comprometido-$pac->devengado)/1.12),2)}}"
                                                   class="form-control input-sm tip"
                                                   data-placement="top" title="Valor sin incluir IVA"
                                                   style="text-align: center">
                                        </td>
                                    </tr>
                                    <tr id='row1'></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <a id="add_row" class="btn btn-success pull-left tip" data-placement="top" title="Agregar fila"><i
                                    class="fa fa-plus" aria-hidden="true"></i>
                        </a><a id='delete_row' class="pull-right btn btn-danger" data-placement="top"
                               title="Eliminar fila"> <i class="fa fa-minus" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-6 col-md-6">
                <label for="" class="control-label">Reforma en:*</label>
                <div class="form-group">
                    <label class="checkbox-inline"><input type="checkbox" name="motivo[]" value="partida">partida</label>
                    <label class="checkbox-inline"><input type="checkbox" name="motivo[]" value="cpc">cpc</label>
                    <label class="checkbox-inline"><input type="checkbox" name="motivo[]" value="tipo compra">tipo compra</label>
                    <label class="checkbox-inline"><input type="checkbox" name="motivo[]" value="detalle">detalle</label>
                    <label class="checkbox-inline"><input type="checkbox" name="motivo[]" value="monto">monto</label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-4">
                <div class="pull-left">
                    <a href="{{route('admin.pacs.index')}}" class="btn btn-default tip" data-placement="top"
                       title="Regresar" style="border-radius: 20px;"><span aria-hidden="true"><i
                                    class="fa fa-arrow-left"></i> Regresar</span></a>
                </div>
                <div class="pull-right">
                    <a href="#" target="_blank">
                        <button class="btn btn-primary tip" id="guardar" data-placement="top"
                                style="border-radius: 20px;"
                                type="submit" title="Guardar y Generar PDF">Guardar
                            <span aria-hidden="true"><i class="fa fa-file-pdf-o"></i></span>
                        </button>
                    </a>
                </div>
            </div>

        </div>

        {!! Form::close() !!}

    </div>
    @include('srpac.searchPAC-modal')
@endsection

@section('scripts')
    <script>

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
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
            $("#form_srpacs").submit(function (e) {
                $("#guardar").prop("disabled", true);
            });
        });

        $(document).ready(function () {
            var i = 1;
            $("#add_row").click(function () {
                $('#row' + i).html("<td style='vertical-align: middle'><input type='hidden' readonly name='pac_id_destino[]'>" + (i + 1) + "</td><td style='vertical-align: middle'><div class='input-group'><input type='number' readonly class='form-control input-sm' name='cod_item[]' placeholder='Partida' style='text-align: center' value=''><span class='input-group-btn'><button class='btn btn-default btn-sm tip' type='button' data-placement='top' title='Buscar Pac' data-toggle='modal' data-target='#searchPAC'><i class='fa fa-search'></i></button></span></div></td><td style='vertical-align: middle'><input  name='cpc[]' type='number' style='text-align: center' class='form-control input-sd'></td><td style='vertical-align: middle'><select name='tipo_compra[]' class='form-control input-sm'><option value='BIEN'>BIEN</option><option value='OBRA'>OBRA</option><option value='SERVICIO'>SERVICIO</option><option value='CONSULTORIA'>CONSULTORIA</option></select></td><td style='vertical-align: middle'><textarea name='concepto[]' rows='4' style='width: 100%;height: 100%; text-transform: uppercase; text-align: justify' class='form-control input-sm'></textarea></td><td style='vertical-align: middle'><input type='number' name='presupuesto[]' step='0.01' class='form-control input-sm tip' data-placement='top' title='Valor sin incluir IVA' style='text-align: center' value=''></td>");

                $('#tabla_modificado').append('<tr id="row' + (i + 1) + '"></tr>');
                i++;
            });
            $("#delete_row").click(function () {
                if (i > 0) {
                    $("#row" + (i - 1)).html('');
                    i--;
                }
            });

        });

        $(document).ready(function () {
            //enviar info al modal antes de cargarlo
            $('#searchPAC').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // boton del que lanza el modal
                var fila_id = button.closest('tr').prop('id');
//                var fila = button.data('id'); // extraer info id del pac data-* attributes
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);
                modal.find('.modal-content .fila_pac_modificado').val(fila_id);
            });

        });

        //en modal search , click en el boton de pac deseado para destino
        $(document).on('click', '.add_pac', function (event) {
            var fila_pac_mod_id = $('.fila_pac_modificado').val();//esta es la fila que se habia creado en el pac_modificado
            var button = $(this); //boton de agregar pac al destino
            var pac_id_destino = button.data('id');//id del pac que se selecciono como destino
            var valores = [];
            // Obtenemos todos los valores del tr ,contenidos en los <td> con clases valores
            $(this).parents("tr").find(".valores").each(function () {
                valores.push($(this).text());
            });

            var fila_pac_mod = $("#" + fila_pac_mod_id);
            if ($.isArray(valores)) {
                fila_pac_mod.find('td:eq(0)>input').val(pac_id_destino);
                fila_pac_mod.find('td:eq(1)>div>input').val(valores[0]);
                fila_pac_mod.find('td:eq(2)>input').val(valores[1]);
                fila_pac_mod.find('td:eq(3)>select').val(valores[2]);
                fila_pac_mod.find('td:eq(4)>textarea').val(valores[3]);
                fila_pac_mod.find('td:eq(5)>input').val(valores[4]);
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


    </script>
@endsection