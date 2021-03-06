<!--Edit departamento  Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="modalDepEdit"
     aria-describedby="Departamentos FDG">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDepEdit">Editar Coordinación</h4>
            </div>
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                {!! Form::open(['class'=>'form_noEnter', 'id'=>'form-update']) !!}
                {!! Form::hidden('dep_id',null,['id'=>'dep_id']) !!}
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('area_edit','Dirección') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::select('area_edit',$list_areas,null,['class'=>'form-control','placeholder'=>'Seleccione el área','required']) !!}
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('dep_edit','Coordinación') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('dep_edit',null,['class'=>'form-control', 'placeholder'=>'Nombre de la coordinación','style'=>'text-transform:uppercase','required']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                {{--</div>--}}
            </div><!--./modal body-->
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-primary actualizarDep">Actualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
