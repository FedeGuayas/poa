@extends('layouts.master')
@section('tile','Roles')
@section('breadcrumbs', Breadcrumbs::render('roles'))

@section('content')

    <div class="row">
        <div class="col-lg-6">

            @include('alert.alert')

            <div class="form-group">
                @permission('admin-roles')
                <a href="{{route('admin.roles.create')}}" class="btn btn-primary tip" data-placement="right"
                   title="Crear Roles">
                    Crear <i class="fa fa-plus"></i>
                </a>
                @endpermission
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-lg-offset-1 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover highlight">
                <thead>
                <th>Id</th>
                <th>Rol</th>
                <th>Descripción</th>
                <th>Permisos</th>
                <th>Opciones</th>
                </thead>
                @foreach ($roles as $rol)
                    <tr>
                        <td>{{ $rol->id }}</td>
                        <td>{{ $rol->display_name }}</td>
                        <td>{{ $rol->description }}</td>
                        <td>
                            @foreach($rol->perms as $per)
                               <i class="fa fa-caret-right"></i> {{ $per->display_name }}<br>
                            @endforeach
                        </td>

                        <td>
                            @permission('admin-roles')
                            <a href="#!" data-target="#modal-delete-{{ $rol->id }}" data-toggle="modal"
                               class="btn btn-xs btn-danger tip" data-placement="top" title="Eliminar"><i
                                        class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $rol->id ) }}"
                               class="btn btn-xs btn-success tip" data-placement="top" title="Editar">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </a>
                            @endpermission
                            <a href="{{ route('admin.roles.perms',$rol->id  ) }}" class="btn btn-xs btn-primary tip "
                               data-placement="top" title="Asignar permisos">
                                <i class="fa fa-key" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                    @include ('roles.modal')
                @endforeach
            </table><!--end table-responsive-->
        </div><!-- end div ./table-responsive-->
    </div><!--end div ./col-lg-12. etc-->



@endsection
@section('scripts')
    <script>
        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });
    </script>
@endsection