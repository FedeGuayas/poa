<div class="modal fade" id="edit-modal-{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="modalItemEdit"
     aria-describedby="Items">
    <div class="modal-dialog modal-lg" role="document" style=" width: 70% !important;">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalItemEdit-{{$item->id}}">Editar Item</h4>
            </div>
            {!! Form::model($item,['route'=>['admin.items.update',$item->id], 'method'=>'PUT','class'=>'form_noEnter','id'=>'form-update']) !!}
            <div class="modal-body">
                {!! Form::hidden('item_id',$item->id,['id'=>'item_id']) !!}
                <div class="row">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('programa_edit','Programa:') !!}
                                    {!! Form::select('programa_edit',$list_programs,$item->actividad_programa->programa_id,['class'=>'form-control','placeholder'=>'Seleccione programa...','id'=>'programa_edit']) !!}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('actividad_edit','Actividades:') !!}
                                    {!! Form::select('actividad_edit',$list_actividades,$item->actividad_programa->actividad_id,['class'=>'form-control','id'=>'actividad_edit','placeholder'=>'Seleccione actividad...']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('codigo_edit','Código') !!}
                                    {!! Form::number('codigo_edit',$item->cod_item,['step' => '1','min'=>'1','class'=>'form-control text-uppercase','placeholder'=>'Código','required']) !!}
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('item_edit','Item') !!}
                                    {!! Form::text('item_edit',$item->item,['class'=>'form-control text-uppercase','placeholder'=>'Item','required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                {!! Form::label('presupuesto_edit','Presupuesto') !!} <span
                                        class="text-danger fa-lg">*</span>
                                <div class="input-group has-success">
                                    <span class="input-group-addon"><i class="fa fa-dollar text-primary"></i></span>
                                    {!! Form::text('presupuesto_edit',$item->presupuesto,['class'=>'form-control text-uppercase','placeholder'=>'$ 0.00','required']) !!}
                                    {{--{!! Form::number('total_disponible',null,['class'=>'form-control tip','data-placement'=>'top','title'=>'A distribuir','placeholder'=>'0.00','id'=>'total_disponible','readonly']) !!}--}}
                                    <span class="input-group-addon">.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--./modal body-->
            <div class="modal-footer bg-success">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="reset" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
