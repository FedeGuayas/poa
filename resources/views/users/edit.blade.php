<!--CREATE AREA  Modal -->
<div class="modal fade" id="edit-modal-{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="modalUserEdit"
     aria-describedby="Editar Usuario">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalUserEdit-{{$u->id}}">Editar Usuario.</h4>
            </div>
            {!! Form::model($u,['route'=>['users.update',$u->id],'method'=>'PUT','class'=>'form_noEnter','autocomplete'=>'off','files'=>'true']) !!}
            <div class="modal-body">
                {{--<div style="overflow:auto;">--}}
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            {!! Form::label('name','Nombre*') !!}
                            {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Nombre de usuario','style'=>'text-transform:uppercase','required','value'=>"{old('name')}"]) !!}
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            {!! Form::label('email','Email:') !!}
                            {!! Form::email('email',null,['class'=>'form-control','placeholder'=>'ejemplo@mail.com']) !!}
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Contraseña</label>
                            <input id="password" type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label">Confirme la contraseña</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                            @endif

                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('imagen','Imagen:') !!}
                            {!! Form::file('imagen',null,['class'=>'form-control']) !!}
                            @if (empty($u->avatar))
                                <img src="{{asset('dist/img/avatar5.png')}}" alt="" style='height: auto; width: 100px;' class="img-thumbnail img-responsive">
                            @else
                                <img src="{{ asset('/dist/img/users/'.$u->avatar)}}" style='width: 100px;' class="img-thumbnail img-responsive">
                            @endif
                        </div>
                    </div>
                </div>
                {{--</div>--}}
            </div><!--./modal body-->
            <div class="modal-footer bg-success">
                <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Actualizar</button>
                <button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-paint-brush"></i> Limpiar</button>
                <button class="btn btn-sm btn-default" data-dismiss="modal"> <i class="fa fa-close"></i>Cerrar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>