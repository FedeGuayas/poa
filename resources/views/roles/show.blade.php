@extends('layouts.dashboard')

@section('page_heading', 'Rol')

@section('section')

    <div class="row">
        <div class="col s12">
            <div>
                <table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table">
                    <thead>
                    <th>Id</th>
                    <th>Rol</th>
                    <th>Nombre para mostrar</th>
                    <th>Descripción</th>
                    <th>Creado</th>
                    <th>Modificado</th>
                    <th>Opciones</th>
                    </thead>
                        <tr>
                            {{--<td>{{ $rol->id }}</td>--}}
                            {{--<td>{{ $rol->name }}</td>--}}
                            {{--<td>{{ $rol->display_name }}</td>--}}
                            {{--<td>{{ $rol->description }}</td>--}}
                            {{--<td>{{ $rol->created_at }}</td>--}}
                            {{--<td>{{ $rol->updated_at }}</td>--}}
                            <td>
                                <a href="{{ route('admin.roles.index') }}">
                                    {!! Form::button('<i class="fa fa-undo"></i>',['class'=>'btn ']) !!}
                                </a>
                            </td>
                        </tr>
                  </table><!--end table-responsive-->
            </div><!-- end div ./table-responsive-->
        </div><!--end div ./col-lg-12. etc-->
    </div><!--end div ./row-->

@endsection