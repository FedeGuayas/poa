<!-- Modal subir CPAC file -->
<div class="modal fade" id="subirCPAC" tabindex="-1" role="dialog" aria-labelledby="subirCPACTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="subirCPACTitle">Cargar archivo de Certificación PAC</h4>
            </div>
            {!! Form::open(['route' =>'pac.postFileCPAC', 'method'=>'post','id'=>'cpac-file-form','files'=>'true']) !!}
            {!! Form::hidden('cpac_pac_id',null,['id'=>'cpac_pac_id']) !!}
            <div class="modal-body">

                <div class="form-group" id="form-group-cpac-file">
                    <label for="cpac-file">Seleccione el PDF de la CPAC</label>
                    <input type="file" name="cpac-file" id="cpac-file" class="form-control-file" aria-describedby="cpacFileHelpBlock" accept="application/pdf">
                    <small id="cpacFileHelpBlock" class="form-text text-muted">
                        El archivo de ser un pdf, su tamaño no debe superar los 100Kb. No se admitiran otros tipos de archivos.
                    </small>
                    <span class="help-block"></span>
                    <p class="error text-center alert alert-danger hidden"></p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Subir archivo <i class="fa fa-file-pdf-o"></i></button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>