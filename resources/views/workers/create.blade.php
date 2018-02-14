<!--CREATE Worker  Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="modalWorkerCreate"
     aria-describedby="Crear Trabajador">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalWorkerCreate">Crear Trabajador</h4>
            </div>
            {!! Form::open(['route'=>'admin.workers.store','class'=>'form_noEnter', 'method'=>'POST', 'autocomplete'=>'off']) !!}
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('area_id','Direcciones:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::select('area_id',$list_areas,null,['class'=>'form-control area_id','placeholder'=>'Dirección ...','required']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('departamento_id','Coordinaciones:') !!} <span class="text-danger fa-lg">*</span> <i class="fa fa-spinner fa-pulse fa-fw text-primary hidden load_dpto_create"></i>
                                {!! Form::select('departamento_id',['placeholder'=>'Seleccione coordinación...'],null,['class'=>'form-control departamento','id'=>'departamento_id']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('nombres','Nombres:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('nombres',null,['class'=>'form-control', 'placeholder'=>'Nombres','style'=>'text-transform:uppercase','required','id'=>'nombres']) !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('apellidos','Apellidos:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('apellidos',null,['class'=>'form-control', 'placeholder'=>'Apellidos','style'=>'text-transform:uppercase','required']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('num_doc','Cédula:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::text('num_doc',null,['class'=>'form-control','id'=>'num_doc','required'])!!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('email','Email:') !!} <span class="text-danger fa-lg">*</span>
                                {!! Form::email('email',null,['class'=>'form-control','placeholder'=>'ejemplo@mail.com','id'=>'email','required'])!!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('cargo','Cargo:') !!}
                                {!! Form::text('cargo',null,['class'=>'form-control','id'=>'cargo'])!!}
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