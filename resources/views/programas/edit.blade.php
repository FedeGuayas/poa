<!--CREATE AREA  Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="modalProgEdit"
     aria-describedby="Programas">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalProgEdit">Editar Programa</h4>
            </div>
            <div class="modal-body">
                <div style="overflow:auto;">
                {!! Form::open(['id'=>'form-update', 'class'=>'form-inline' ]) !!}
                {!! Form::hidden('programa_id',null,['id'=>'programa_id']) !!}
                <div class="form-group">
                    {!! Form::label('codigo_edit','Código') !!}
                    {!! Form::number('codigo_edit',null,['step' => '1','min'=>'1','class'=>'form-control','placeholder'=>'Código','style'=>'text-transform:uppercase; width: 80px','required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('programa_edit','Programa') !!}
                    {!! Form::text('programa_edit',null,['class'=>'form-control','placeholder'=>'Programa','style'=>'text-transform:uppercase; width:600px','required']) !!}
                </div>

                {!! Form::close() !!}
                </div>
            </div><!--./modal body-->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary actualizarProg">Actualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
