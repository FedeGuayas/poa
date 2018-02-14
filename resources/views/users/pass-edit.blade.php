@extends('layouts.master')
@section('tile','Contraseña')
@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')
    <div class="col-md-4 col-sm-6 col-md-offset-4">
        @include('alert.alert')

        <div class="panel panel-primary">

            <div class="panel-heading">
                <h3 class="panel-title">Cambiar Contraseña</h3>
            </div>

            {!! Form::model($user,['route'=>['user.password.update',$user], 'method'=>'PUT','role'=>'form']) !!}
            <div class="panel-body">

                <div class="form-group has-feedback">
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="Contraseña anterior:*" id="password" name="password"  value="{{ old('password') }}">
                        <span class="form-control-feedback"><i class="fa fa-lock" aria-hidden="true"></i></span>
                        @if ($errors->has('password'))
                            <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <div class="form-group{{ $errors->has('password_new') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="Nueva contraseña:*" id="password_new" name="password_new">
                        <span class="form-control-feedback"><i class="fa fa-unlock-alt" aria-hidden="true"></i></span>
                        @if ($errors->has('password_new'))
                            <span class="help-block"><strong>{{ $errors->first('password_new') }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <div class="form-group{{ $errors->has('password_new_confirmation') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="Confirmar contraseña:*" id="password_new_confirmation" name="password_new_confirmation">
                        <span class="form-control-feedback"><i class="fa fa-unlock" aria-hidden="true"></i></span>
                        @if ($errors->has('password_new_confirmation'))
                            <span class="help-block"><strong>{{ $errors->first('password_new_confirmation') }}</strong></span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="panel-footer">

                {!! Form::button('Actualizar <i class="fa fa-save"></i>',['class'=>'btn btn-success','type'=>'submit']) !!}
                {!! Form::button('Cancelar <i class="fa fa-ban"></i>',['class'=>'btn btn-danger','type'=>'reset']) !!}

            </div>

            {!! Form::close() !!}

        </div>

    </div>
@endsection