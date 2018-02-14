@extends('layouts.master')
@section('tile','Crear Rol')
@section('breadcrumbs', Breadcrumbs::render('roles'))

@section('content')

    <div class="row">
        {!! Form::open(['route'=>'admin.roles.store', 'method'=>'POST'])  !!}

        <div class="col-lg-10 col-md-8 col-sm-12">
            <div class="form-horizontal">

                <div class="form-group">
                    {!! Form::label('name','Nombre del rol:',['class'=>'control-label col-sm-2']) !!}
                    <div class="col-lg-6">
                        {!! Form::text('name',null,['class'=>'form-control','placeholder'=>' Ejemplo: "administrador"','required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('display_name','Nombre a mostrar:',['class'=>'control-label col-lg-2']) !!}
                    <div class="col-lg-6">
                        {!! Form::text('display_name',null,['class'=>'form-control','placeholder'=>' Ejemplo: "Administrador del sistema"','style'=>'text-transform:uppercase']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('description','Descripción',['class'=>'control-label col-lg-2']) !!}
                    <div class="col-lg-6">
                        {!! Form::textarea('description',null,['class'=>'form-control','rows'=>'3','style'=>'text-transform:uppercase']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        {!! Form::button('Guardar <i class="fa fa-save" aria-hidden="true"></i>', ['class'=>'btn btn-primary','type' => 'submit']) !!}
                        {!! Form::button('Cancelar <i class="fa fa-ban" aria-hidden="true"></i>',['class'=>'btn btn-danger','type' => 'reset']) !!}
                        <a href="{{route('admin.roles.index')}}">
                            {!! Form::button('Regresar <i class="fa fa-undo" aria-hidden="true"></i>',['class'=>'btn btn-flat']) !!}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection
