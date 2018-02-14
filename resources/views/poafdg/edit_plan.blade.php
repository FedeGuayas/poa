
<div class="modal fade" id="edit-plan" tabindex="-1" role="dialog" aria-labelledby="modalPlanEdit"
     aria-describedby="Items">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalPlanEdit">Editar Presupuesto</h4>
            </div>
{{--            {!! Form::model($item,['route'=>['admin.poa.update',$item->id], 'method'=>'PUT','class'=>'form_noEnter', 'id'=>"form-poa-update{$item->id}"]) !!}--}}
{{--            {!! Form::open(['route'=>['admin.poa.index'], 'method'=>'get']) !!}--}}
            {!! Form::open(['id'=>'form-poa-update', 'class'=>'form-noEnter' ]) !!}
            {!! Form::hidden('item_edit_id',null,['id'=>'item_edit_id']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-offset-2 col-md-offset-1 col-sm-offset-1">
                        <div class="form-group">
                            <div class="input-group has-success small">
                                <span class="input-group-addon"><i class="fa fa-dollar text-warning"></i></span>
                                {!! Form::number('monto_edit',null,['class'=>'form-control','id'=>'monto_edit']) !!}
                                <span class="input-group-addon">.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--./modal body-->
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-primary actualizar">Actualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

