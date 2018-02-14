    <div class="modal fade modal-slide-in-right" aria-hidden="true"
         role="dialog" tabindex="-1" id="modal-delete-{{$per->id}}">

        {!! Form::open(['route'=>['admin.permissions.destroy',$per->id],'method'=>'delete']) !!}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hiden="true">x</span>
                    </button>
                    <h4 class="modal-title">Eliminar Permiso</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea eliminar el permiso</p><em>{{$per->task}}</em>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </div>
        </div>
        {{Form::Close()}}
    </div>

