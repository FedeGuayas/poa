@extends('layouts.master')
@section('title','POA')
@section('breadcrumbs', Breadcrumbs::render('apertura'))

@section('content')

    <div id="poa_tabla" style="overflow: auto;">
        <table class="table" id="poa" width="100%">
            <caption>Federación Deportiva del Guayas</caption>
            <thead>
            <tr>
                <th>Ejercicio</th>
                <th>Programa</th>
                <th>Actividad</th>
                <th>Renglon</th>
                <th>Codificado</th>
                <th>Compromiso</th>
                <th>Devengado</th>
                <th>Pagado</th>
                <th>Disponible</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Ejercicio</th>
                <th>Programa</th>
                <th>Actividad</th>
                <th>Renglon</th>
                <th>Codificado</th>
                <th>Compromiso</th>
                <th>Devengado</th>
                <th>Pagado</th>
                <th>Disponible</th>
            </tr>
            </tfoot>
        </table>
    </div>





@endsection

@section('scripts')

    <script>

        $(document).ready(function(){
            var table=$("#poa").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                searching: true,
                select:true,
                processing: true,
                serverSide: true,
                ajax: "{{route('admin.apertura.index')}}",
                columns:[
                    {data: 'ejercicio'},
                    {data: 'programa'},
                    {data: 'actividad'},
                    {data: 'renglon'},
                    {data: 'codificado'},
                    {data: 'compromiso'},
                    {data: 'devengado'},
                    {data: 'pagado'},
                    {data: 'disponible'}
                ],
                "language": {
                    "decimal": ".",
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
                    },
                    "select": {
                        "rows": {
                            "_": "Ha seleccionado %d filas",
                            "0": "Click en una la fila para seleccionarla",
                            "1": "Solo 1 fila seleccionada"
                        }
                    }
                }
            });

        });

        function ver_poa() {
            var token = $("input[name=_token]").val();
            var route ="{{route('admin.apertura.index')}}";
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
//               contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
//                data: data,
                success: function (response) {
                  //  console.log(response);
//                    $("#poa").html(response);
                },
                error: function (response) {
                }
            });

        }

        $(document).ready(function () {
            ver_poa();

        });


    </script>
@endsection