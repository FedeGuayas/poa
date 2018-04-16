<div class="modal fade" id="solCertificacionesModal" tabindex="-1" role="dialog"
     aria-labelledby="solCertificacionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="solCertificacionesModalLabel">Solicitud de Certificación Presupuestaria y/o
                    PAC </h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' =>['solCertPacPresup'], 'method'=>'get','id'=>'sol-cert-form']) !!}
                {!! Form::hidden('pac_id_sol',null,['id'=>'pac_id_sol']) !!}

                <form>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input type="text" class="form-control tip" data-placement="top"
                                       title="Certificación Presupuestaria" id="user_to_presupuestaria" name="user_to_presupuestaria"
                                       value="kary.morales@fedeguayas.com.ec" readonly>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control tip" data-placement="top"
                                       title="Certificación PAC" id="user_to_cpac" name="user_to_cpac" value="domenique.ulloa@fedeguayas.com.ec"
                                       readonly>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <p>
                            Por favor emitir certificaciones necesarias para:
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="" for="">Información POA del Proceso:</label>
                        <div class="form-inline">
                            <div class="form-group">
                                <input type="text" class="form-control tip" data-placement="top" title="Programa"
                                       id="prog_sol" placeholder="Programa" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control tip" data-placement="top" title="Actividad"
                                       id="actividad_sol" placeholder="Actividad" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control tip" data-placement="top" title="partida"
                                       id="cod_item_sol" placeholder="Partida" readonly>
                            </div>

                        </div>

                        <label for="item_sol">Item:</label>
                        <input type="text" class="form-control input-sm" id="item_sol" readonly>

                        <label for="concepto_sol">Proceso:</label>
                        <input type="text" class="form-control input-sm" id="concepto_sol" readonly>

                        <label for="monto_sol">Monto referencial: (sin inc. IVA):</label>
                        <div class="form-inline">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    {!! Form::text('monto_sol',null,['class'=>'form-control', 'id'=>'monto_sol','readonly']) !!}
                                    <div class="input-group-addon">más IVA</div>
                                </div>
                            </div>
                        </div>

                        <label for="notas_sol" class="control-label">Mensaje:</label>
                        <textarea class="form-control" id="notas_sol" name="notas_sol" placeholder="Observaciones"></textarea>
                    </div>
                </form>
                {{--{!! Form::close() !!}--}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="send_solicitud_certificacion" data-dismiss="modal">
                    Enviar correo <i
                            class="fa fa-envelope"></i></button>
            </div>
        </div>
    </div>
</div>
