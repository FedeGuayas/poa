<!-- Modal Subir SRPAC -->
<div class="modal fade" id="subirSRPAC" tabindex="-1" role="dialog" aria-labelledby="subiSRPACTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="subirSRPACTitle">Cargar archivo de Solicitud Reforma PAC</h4>
            </div>
            {!! Form::open(['route' =>'pac.postFileSRPAC', 'method'=>'post', 'id'=>'srpac-file-form','files'=>'true']) !!}
            {!! Form::hidden('srpac_pac_id',null,['id'=>'srpac_pac_id']) !!}
            <div class="modal-body">

                <div class="form-group" id="form-group-srpac-file">
                    <label for="srpac-file">Seleccione el PDF de la SRPAC</label>
                    <input type="file" name="srpac-file" id="srpac-file" class="form-control-file" aria-describedby="srpacFileHelpBlock" accept="application/pdf">
                    <small id="srpacFileHelpBlock" class="form-text text-muted">
                        El archivo de ser un pdf, su tamaño no debe superar los 100Kb. No se admitiran otros tipos de archivos.
                    </small>
                    <span class="help-block"></span>
                    <p class="error text-center alert alert-danger hidden"></p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success">Subir archivo <i class="fa fa-file-pdf-o"></i></button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>