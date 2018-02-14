<!--CREATE AREA  Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="modalActEdit"
     aria-describedby="Actividades">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalActEdit">Editar Actividad</h4>
            </div>
            <div class="modal-body">
                <div style="overflow:auto;">
                {!! Form::open(['id'=>'form-update', 'class'=>'form-inline' ]) !!}
                {!! Form::hidden('actividad_id',null,['id'=>'actividad_id']) !!}
                <div class="form-group">
                    {!! Form::label('codigo_edit','Código') !!}
                    {!! Form::number('codigo_edit',null,['step' => '1','min'=>'1','class'=>'form-control text-uppercase','placeholder'=>'Código','required', 'style'=>'width: 80px']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('actividad_edit','Actividad') !!}
                    {!! Form::text('actividad_edit',null,['class'=>'form-control text-uppercase','placeholder'=>'Actividad','required','style'=>'width: 600px']) !!}
                </div>

                {!! Form::close() !!}
                </div>
            </div><!--./modal body-->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary actualizarAct">Actualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
