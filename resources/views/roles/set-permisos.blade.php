@extends('layouts.master')
@section('tile','Asignar Permisos al Roles')
@section('breadcrumbs', Breadcrumbs::render('roles'))

@section('content')

    <div class="row">
        <div class="col-sm-8 col-lg-offset-1">
            <div class="alert alert-danger" role="alert">Permisos para  <strong>" {{$rol->display_name}}, " ( {{$rol->name}} )</strong></div>

            {!! Form::model($rol,['route'=>['admin.roles.setpermisos', $rol->id], 'method'=>'PUT','role'=>'form']) !!}
            <table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table">
                <thead>
                    <th>Id</th>
                    <th>Add/Rem</th>
                    <th>Permisos</th>
                    <th>Descripción</th>
                </thead>
                {{--@foreach($role_permissions as $rp) <p>{{$rp->display_name}}</p> @endforeach--}}
                @foreach ($permissions as $per)
                    <tr>
                        <td>{{ $per->id }}</td>
                        <td>
                            {!! Form::checkbox('permissions[]',$per->id,null,['id'=>$per->id]) !!}
                            {{--{!! Form::label($per->id, $per->name) !!}--}}
                        </td>
                        <td>{{ $per->display_name }}</td>
                        <td>{{ $per->description }}</td>
                    </tr>

                @endforeach
            </table><!--end table-responsive-->
            {!! Form::button('Actualizar <i class="fa fa-save"></i>', ['class'=>'btn btn-primary','type' => 'submit']) !!}
            {!! Form::button('Cancelar <i class="fa fa-band"></i>',['class'=>'btn btn-danger','type' => 'reset']) !!}
            <a href="{{ route('admin.roles.index') }}"  class="tooltipped" data-position="top" data-delay="50" data-tooltip="Regresar">
                {!! Form::button('Regresar <i class="fa fa-undo"></i>',['class'=>'btn btn-flat']) !!}
            </a>
            {!! Form::open() !!}
        </div><!--end div ./col-lg-12. etc-->
    </div><!--end div ./row-->


@endsection

