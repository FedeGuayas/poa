<!-- Modal User Image -->
<div class="modal fade" id="permitReform" tabindex="-1" role="dialog" aria-labelledby="permitReformTitle"
     aria-hidden="true">
    <div class="modal-dialog border-primary" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="permitReformTitle">Permitir reformas sobre monto que no será utilizado</h4>
            </div>
            {!! Form::open(['route' =>['pac.permitReform',$pac->id], 'method'=>'put','id'=>'permit-reform-form']) !!}

            <div class="modal-body">
                <div class="form-group">
                    <label for="user-from" class="control-label">De:</label>
                    <input type="text" class="form-control" id="user-from" readonly name="user-from"
                           value="{{$pac->worker->nombres.' '.$pac->worker->apellidos.' ( '.$pac->worker->email.' )'}}">
                    <small id="userFromHelpBlock" class="form-text text-muted">
                        Usuario responsable del PAC.
                    </small>
                </div>
                <div class="form-group">
                    <label for="users-to" class="control-label">Para:</label>
                    <input type="text" class="form-control" id="users-to" name="user-to" readonly value="@foreach ($workers as $worker){{$worker->user->email}};@endforeach
                    ">
                    <small id="userToHelpBlock" class="form-text text-muted">
                        Usuario con permisos de realizar reformas en el area.
                    </small>
                </div>
                <div class="form-group">
                    <label for="message-note" class="control-label">Nota:</label>
                    <textarea class="form-control" id="message-note"  name="message-note" readonly>Estimada(o), se informa que se acaban de liberar para reforma el valor de: $ {{$pac->disponible}}, del proceso PAC: {{$pac->concepto}}, CÓDIGO POA: {{$pac->area_item->item->cod_programa .'-'.$pac->area_item->item->cod_actividad.'-'.$pac->area_item->item->cod_item}}</textarea>
                    <small id="messageTextBlock" class="form-text text-muted">
                        Nota obligatoria
                    </small>
                </div>
                <div class="form-group">
                    <label for="message-text" class="control-label">Mensaje:</label>
                    <textarea class="form-control" id="message-text" name="message-text" style="text-transform: uppercase"></textarea>
                    <small id="messageTextBlock" class="form-text text-muted">
                        Breve mensaje del correo que le llegará a los usuarios que pueden realizar reformas. Opcional
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Enviar <i class="fa fa-send-o"></i></button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>