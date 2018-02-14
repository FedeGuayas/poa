<!-- Modal User Image -->
<div class="modal fade" id="subirCPAC-{{$pac->id}}" tabindex="-1" role="dialog" aria-labelledby="subirCPACTitle"
     aria-hidden="true">
    <div class="modal-dialog card border-primary" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-primary mb-3">
                <h5 class="modal-title" id="subirCPACTitle">Cargar archivo de Certificación PAC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['route' =>'pac.postFileCPAC', 'method'=>'PUT','id'=>'cpac-file-form','files'=>'true']) !!}
            {!! Form::hidden('cpac_pac_id',$pac->id) !!}
            <div class="modal-body">

                <div class="form-group">
                    <label for="cpac-file">Seleccione el PDF de la CPAC</label>
                    <input type="file" name="cpac-file" class="form-control-file" aria-describedby="cpacFileHelpBlock" accept="application/pdf">
                    <small id="cpacFileHelpBlock" class="form-text text-muted">
                        El archivo de ser un pdf, su tamaño no debe superar los 100Kb. No se admitiran otros tipos de archivos.
                    </small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Subir archico <i class="fa fa-file-pdf-o"></i></button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>