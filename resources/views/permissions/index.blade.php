@extends('layouts.master')
@section('tile','Permisos')
@section('breadcrumbs', Breadcrumbs::render('permisos'))

@section('content')


    <div class="row">
        <div class="col-lg-6">
            @include('alert.alert')

            <div class="form-group">
                @permission('admin-permisos')
                <a href="{{route('admin.permissions.create')}}" class="btn btn-primary tip" data-placement="right"
                   title="Crear Permiso">
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
                {{--<th>Permiso</th>--}}
                <th>Nombre del permiso</th>
                <th>Descripción</th>
                <th>Opciones</th>
                </thead>
                @foreach ($permisos as $per)
                    <tr>
                        <td>{{ $per->id }}</td>
                        {{--<td>{{ $per->name }}</td>--}}
                        <td>{{ $per->display_name }}</td>
                        <td>{{ $per->description }}</td>
                        <td>
                            @permission('admin-permisos')
                            <a href="#!" data-target="#modal-delete-{{ $per->id }}" data-toggle="modal"
                               class="btn btn-xs btn-danger tip" data-placement="top" title="Eliminar"><i
                                        class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('admin.permissions.edit', $per->id ) }}"
                               class="btn btn-xs btn-success tip" data-placement="top" title="Editar">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </a>
                            @endpermission
                        </td>
                    </tr>
                    @include ('permissions.modal')
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