<!--CREATE Departamento  Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="modaldepartamentoCreate"
     aria-describedby="Crear Coordinación">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDepartamentoCreate">Crear Coordinación</h4>
            </div>
            {!! Form::open(['route'=>'admin.departamentos.store','class'=>'form_noEnter', 'method'=>'POST','autocomplete'=>'off']) !!}
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('area','Dirección') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::select('area',$list_areas,null,['class'=>'form-control','placeholder'=>'Seleccione el área','required']) !!}
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('departamento','Coordinación') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('departamento',null,['class'=>'form-control', 'placeholder'=>'Nombre de la coordinación','style'=>'text-transform:uppercase','required']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--./modal body-->
            <div class="modal-footer bg-primary">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save" aria-hidden="true"></i> Guardar</button>
                <button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-paint-brush"></i> Limpiar</button>
                <button class="btn btn-sm btn-default" data-dismiss="modal"> <i class="fa fa-close"></i>Cerrar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>