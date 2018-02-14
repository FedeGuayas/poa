@extends('layouts.master')
@section('title','Login')

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-md-offset-4">

            <div class="panel panel-primary">

                <div class="panel-heading">
                    <h3 class="panel-title">Acceder al sistema</h3>
                </div>

                <form role="form" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                <div class="panel-body">

                    <div class="form-group has-feedback">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <input type="email" class="form-control" placeholder="Email" id="email" name="email"
                                   value="{{ old('email') }}">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                                <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                            @endif
                        </div>
                    </div>

                        <div class="form-group has-feedback">
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" id="password" class="form-control" name="password"
                                       placeholder="Contraseña">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                @if ($errors->has('password'))
                                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong> </span>
                                @endif
                            </div>
                        </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Recordarme
                            </label>
                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-btn fa-sign-in"></i> Entrar
                    </button>
                    <a class="btn btn-link pull-right" href="{{ url('/password/reset') }}">Olvido su contraseña?</a><br>

                </div>

                </form>


            </div>
        </div>
    </div>

@endsection

