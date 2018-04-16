<!-- Modal Subir Inc PAC-->
<div class="modal fade" id="subirIncPAC" tabindex="-1" role="dialog" aria-labelledby="subiIncPACTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="subiIncPACTitle">Cargar archivo de Inclusión PAC</h4>
            </div>
            {!! Form::open(['route' =>'pac.inclusion.postFileIncPAC', 'id'=>'incpac-file-form','method'=>'post','files'=>'true']) !!}
            {!! Form::hidden('inclusion_pac_id',null,['id'=>'inclusion_pac_id']) !!}
            <div class="modal-body">

                <div class="form-group" id="form-group-incpac-file">
                    <label for="incpac-file">Seleccione el PDF de la Inclusión PAC</label>
                    <input type="file" name="incpac-file" id="incpac-file" class="form-control-file" aria-describedby="incpacFileHelpBlock" accept="application/pdf">
                    <small id="incpacFileHelpBlock" class="form-text text-muted">
                        El archivo de ser un pdf, su tamaño no debe superar los 100Kb. No se admitiran otros tipos de archivos.
                    </small>
                    <span class="help-block"></span>
                    <p class="error text-center alert alert-danger hidden"></p>
                </div>

            </div>
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success">Subir archivo <i class="fa fa-file-pdf-o"></i></button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>