<!--CREATE Items  Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="modalItemsCreate"
     aria-describedby="Crear Item">
    <div class="modal-dialog modal-lg" role="document" style=" width: 70% !important;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalItemCreate">Crear Item</h4>
            </div>
            {!! Form::open(['class'=>'form_noEnter', 'id'=>'form_item_store']) !!}
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                <div class="row">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('programa','Programa:') !!} <span class="text-danger fa-lg">*</span>
                                    {!! Form::select('programa',$list_programs,null,['class'=>'form-control','placeholder'=>'Seleccione programa...','id'=>'programa']) !!}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('actividad','Actividades:') !!} <span class="text-danger fa-lg">*</span> <i class="fa fa-spinner fa-pulse fa-fw text-primary hidden load_dpto_create"></i>
                                    {!! Form::select('actividad',['placeholder'=>'Seleccione actividad...'],null,['class'=>'form-control','id'=>'actividad']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('codigo','Código') !!} <span class="text-danger fa-lg">*</span>
                                    {!! Form::number('codigo',null,['step' => '1','min'=>'1','class'=>'form-control text-uppercase','placeholder'=>'Código','required']) !!}
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('item','Item') !!} <span class="text-danger fa-lg">*</span>
                                    {!! Form::text('item',null,['class'=>'form-control text-uppercase','placeholder'=>'Item','required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                             <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                {!! Form::label('presupuesto','Presupuesto') !!} <span class="text-danger fa-lg">*</span>
                                <div class="input-group has-success">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-primary"></i></span>
                                    {!! Form::number('presupuesto',null,['class'=>'form-control text-uppercase','placeholder'=>'0.00','required']) !!}
                                    {{--{!! Form::number('total_disponible',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'total_disponible','readonly']) !!}--}}
                                    <span class="input-group-addon">.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--./modal body-->
            <div class="modal-footer bg-primary">
                <button type="button" class="btn btn-sm btn-success guardar"><i class="fa fa-save" aria-hidden="true"></i> Guardar</button>
                <button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-paint-brush"></i> Limpiar</button>
                <button class="btn btn-sm btn-default" data-dismiss="modal"> <i class="fa fa-close"></i>Cerrar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>