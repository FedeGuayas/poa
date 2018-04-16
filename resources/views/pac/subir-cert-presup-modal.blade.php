<!-- Modal User Image -->
<div class="modal fade" id="subirCPresup" tabindex="-1" role="dialog" aria-labelledby="subirCPresupTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="subirCPresupTitle">Cargar archivo de Certificación Presupuestaria</h4>
            </div>

            {!! Form::open(['route' =>'pac.postFileCPresup', 'method'=>'post','id'=>'cpresup-file-form','files'=>'true']) !!}
            {!! Form::hidden('cpresup_pac_id',null,['id'=>'cpresup_pac_id']) !!}
            <div class="modal-body">
                <div class="form-group" id="form-group-cpresup-file">
                    <label for="cpresup-file">Seleccione el PDF de la Certificación Presupuestaria</label>
                    <input type="file" name="cpresup-file" id="cpresup-file" class="form-control-file" aria-describedby="cpresupFileHelpBlock" accept="application/pdf">
                    <small id="cpresupFileHelpBlock" class="form-text text-muted">
                        El archivo de ser un pdf, su tamaño no debe superar los 100Kb. No se admitiran otros tipos de archivos.
                    </small>
                    <span class="help-block"></span>
                    <p class="error text-center alert alert-danger hidden"></p>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" id="form-group-cod_cpresup">
                            <label for="cod_cpresup">Código Cert. Presupuestaria</label>
                            <input type="text" name="cod_cpresup" id="cod_cpresup" class="form-control">
                            <span class="help-block"></span>
                            <p class="error text-center alert alert-danger hidden"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="upload_cert_presup">Subir archivo <i class="fa fa-file-pdf-o"></i></button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
