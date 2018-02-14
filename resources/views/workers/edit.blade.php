<!--EDIT WORKER  Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="modalWorkerEdit"
     aria-describedby="Trabajadores FDG">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalWorkerEdit">Editar Trabajador</h4>
            </div>
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                {!! Form::open(['class'=>'form_noEnter', 'id'=>'form-update']) !!}
                {!! Form::hidden('worker_id',null,['id'=>'worker_id']) !!}
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('area_edit','Direcciones:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::select('area_edit',$list_areas,null,['class'=>'form-control area_id','placeholder'=>'Dirección ...','id'=>'area_edit']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('departamento_edit','Coordinaciones:') !!} <span class="text-danger fa-lg">*</span> <i class="fa fa-spinner fa-pulse fa-fw text-primary hidden load_dpto_create"></i>
                                {!! Form::select('departamento_edit',$list_departamentos,null,['class'=>'form-control departamento','placeholder'=>'Seleccione coordinación...','required','id'=>'departamento_edit','disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('nombres_edit','Nombres:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('nombres_edit',null,['class'=>'form-control','style'=>'text-transform:uppercase','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('apellidos_edit','Apellidos:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('apellidos_edit',null,['class'=>'form-control', 'placeholder'=>'Apellidos','id'=>'apellidos_edit','style'=>'text-transform:uppercase','required']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('num_doc_edit','Cédula:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('num_doc_edit',null,['class'=>'form-control','id'=>'num_doc_edit','required'])!!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('email_edit','Email:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::email('email_edit',null,['class'=>'form-control','placeholder'=>'ejemplo@mail.com','id'=>'email_edit','required'])!!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('cargo_edit','Cargo:') !!}
                                {!! Form::text('cargo_edit',null,['class'=>'form-control','id'=>'cargo_edit'])!!}
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                {{--</div>--}}
            </div><!--./modal body-->
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-primary actualizarWorker">Actualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
