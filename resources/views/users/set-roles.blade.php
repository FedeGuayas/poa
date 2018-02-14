@extends('layouts.master')
@section('tile','Roles')
@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="alert alert-danger" role="alert">Accesos de <strong>{{$nombre}}</strong></div>

            {!! Form::model($user,['route'=>['admin.users.setroles', $user->id], 'method'=>'PUT','role'=>'form']) !!}
            <table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table">
                <thead>
                <th>Id</th>
                <th>Add/Rem</th>
                <th>Rol</th>
                <th>Descripción</th>
                <th>Permiso</th>
                </thead>

                @foreach ($roles as $rol)
                    {{--Si el rol es root y el usuario autenticado no es root, no mostrarlo--}}
                    @if ($rol->name=='root' && !Auth::user()->hasRole('root'))
                        @continue
                    @endif
                    {{--Si el rol no es responsable-poa, responsable-pac o analista y el usuario autenticado es responsable-poa, no mostrarlo--}}
                    @if (!($rol->name=='responsable-poa' || $rol->name=='responsable-pac' || $rol->name=='analista') && Auth::user()->hasRole('responsable-poa'))
                        @continue
                    @endif
                    <tr>
                        <td>{{ $rol->id }}</td>
                        <td>{!! Form::checkbox('roles[]',$rol->id,null,['id'=>$rol->id]) !!}
                        {!! Form::label($rol->id, $rol->name) !!}
                        <td>{{ $rol->display_name }}</td>
                        <td>{{ $rol->description }}</td>
                        <td>
                            @foreach($rol->perms as $perm)
                                <i class="fa fa-caret-right"></i> {{ $perm->display_name }}<br>
                            @endforeach
                        </td>

                    </tr>
                @endforeach
            </table><!--end table-responsive-->
            {!! Form::button('Actualizar <i class="fa fa-save"></i>', ['class'=>'btn btn-primary','type' => 'submit']) !!}
            {!! Form::button('Cancelar <i class="fa fa-ban"></i>',['class'=>'btn btn-danger','type' => 'reset']) !!}
            <a href="{{ route('admin.users.index') }}" class="tooltipped" data-position="top" data-delay="50"
               data-tooltip="Regresar">
                {!! Form::button('Regresar <i class="fa fa-undo"></i>',['class'=>'btn btn-flat']) !!}
            </a>
            {!! Form::open() !!}
        </div><!--end div ./col-lg-12. etc-->
    </div><!--end div ./row-->


@endsection

