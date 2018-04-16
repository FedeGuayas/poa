<div class="modal fade" id="tipoReformaModal" tabindex="-1" role="dialog" aria-labelledby="tipoReformaModalTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="tipoReformaModalTitle">Tipo de reforma a ejecutar</h5>
            </div>

            {!! Form::open(['route' =>['createReforma'], 'method'=>'get','id'=>'reform-form']) !!}
            {!! Form::hidden('to_reform_pac_id',null,['id'=>'to_reform_pac_id']) !!}

            <div class="modal-body">

                <div class="form-group">
                    {!! Form::label('reform_type','Seleccione el tipo de reforma') !!}
                    {!! Form::select('reform_type',$list_reformas, null, ['class' => 'form-control','id'=>'reform_type']) !!}
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="send_reform" data-dismiss="modal">Aceptar <i
                            class="fa fa-check-circle"></i></button>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
</div>