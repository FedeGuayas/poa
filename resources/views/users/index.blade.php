@extends('layouts.master')
@section('tile','Usuarios')
@section('breadcrumbs', Breadcrumbs::render('inicio'))

@section('content')



    <div class="col-lg-8 col-lg-offset-2">
        @include('alert.alert')
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover" id="users_table"
                   cellspacing="0" style="display: none;" data-order='[ 0, "asc" ]'>
                <thead>
                <th >Usuario</th>
                <th >Email</th>
                <th >Rol</th>
                </thead>
                <tfoot>
                <th class="search-filter">Usuario</th>
                <th class="search-filter">correo@mail.com</th>
                <th>Accion</th>
                </tfoot>
                <tbody>
                @foreach($users as $u)
                    {{--Si el usuario a listar es root y el usuario autenticado no es root , no mostrarlo--}}
                    @if ($u->hasRole('root') && !Auth::user()->hasRole('root'))
                        @continue
                    @endif
                    {{--Si el usuario autenticado es responsable-poa y el usuario a listar no es de su area, no mostrarlo--}}
                    @if (Auth::user()->hasRole(['responsable-poa']) && Auth::user()->worker->departamento->area_id!=$u->worker->departamento->area_id)
                        @continue
                    @endif
                    <tr>
                        <td>{{$u->worker['apellidos'].' '.$u->worker['nombres']}}</td>
                        <td>{{$u->email}}</td>
                        <td>
                            <a href="{{ route('admin.users.roles', $u->id )}}" class="btn btn-xs btn-primary tip" data-placement="top" title="Roles"><i
                                        class="fa fa-key" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            cargarDatatable();
        });

        function cargarDatatable() {
            var table = $("#users_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                "language": {
                    "decimal": "",
                    "emptyTable": "No se encontraron datos en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrados de un total _MAX_ registros)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encrontraron coincidencias",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Activar para ordenar ascendentemente",
                        "sortDescending": ": Activar para ordenar descendentemente"
                    }
                }
            });

            $("#users_table").fadeIn();

            $('#users_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" class="input-sm" style="width: 80%;" placeholder="' + title + '" />');
            });
            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

    </script>

@endsection

