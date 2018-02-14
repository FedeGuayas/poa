@extends('layouts.master')
@section('title','POA-AREA')

@section('breadcrumbs', Breadcrumbs::render('plan-area'))

@section('content')
    @include('alert.alert')
    @include('alert.alert_json')
    {!! Form::open(['route'=>['admin.poa.index'],'method'=>'GET','class'=>'form_noEnter']) !!}
    <div class="form-inline">
        <div class="form-group">
            {{--{!! Form::label('area','Areas') !!}--}}
            {!! Form::select('area',$list_areas,$area_select,['class'=>'form-control','placeholder'=>'Direcciones...','id'=>'area']) !!}
        </div>
        {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Buscar','id'=>'buscar', 'type'=>'submit']) !!}
    </div>
    {!! Form::close() !!}
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading clearfix">POA - {{count($area)>0 ? $area->area : ""}}
                    <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                       aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
                </div>
                <div class="panel-body collapse in" id="resumen">
                    <div class="row">
                        <div class="container col-lg-6" id="botones_imprimir">
                            {{--{!! Form::open(['route'=>['admin.pacs.pac-pdf'],'method'=>'GET','id'=>'form_imprimir']) !!}--}}
                            {{--<a href="#!" class="btn btn-default tip" type="submit" data-placement="top" title="Imprimir" target="_blank" id="imprimir_pdf"><i class="fa fa-print"></i></a>--}}
                            {{--{!! Form::close() !!}--}}
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-hover highlight" id="area_table" width="100%" style="display: none;">
                            <thead>
                                <th>Cod_item</th>
                                <th>Item</th>
                                <th style="width: 40px">Mes</th>
                                <th>Monto</th>
                            </thead>
                            <tfoot>
                            <tr>
                                {{--<th class="search-filter">Cod_Prog</th>--}}
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th class="search-filter">filtrar</th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach($area_item as $item)
                                <tr>
                                    <td>{{$item->cod_programa.'-'.$item->cod_actividad.'-'.$item->cod_item}}</td>
                                    <td>{{$item->item}}</td>
                                    <td>{{$item->month}}</td>
                                    <td>${{number_format($item->monto,2,'.','')}}</td>
                                    {{--<td>--}}
                                        {{--<a href="#!" class="btn-xs btn-success tip" data-placement="top" title="Editar"--}}
                                           {{--data-toggle="modal"--}}
                                           {{--data-target="#edit-plan" onclick="mostrarEdit({{$item->id}})"><i--}}
                                                    {{--class="fa fa-pencil"></i>--}}
                                        {{--</a>--}}
                                        {{--<a href="#!" class="btn-xs btn-danger delete tip" data-placement="top"--}}
                                           {{--title="Eliminar"--}}
                                           {{--data-id="{{$item->id}}"><i class="fa fa-trash-o"></i>--}}
                                        {{--</a>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table><!--end table-responsive-->
                    </div>
                    {{--<label for="Total" class="btn btn-primary pull-right">Total <span class="badge"> $ {{number_format($total,2,'.',' ')}}</span></label>--}}
                        @include('poafdg.edit_plan')

                        {!! Form::open(['route'=>['admin.poa.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
                        {!! Form::close() !!}

                </div>
            </div>{{--./panel-success--}}
        </div>{{--./col-md-12--}}
    </div>


@endsection

@section('scripts')

    <script>



        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        $(document).ready(function () {

            var table=$("#area_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                "language":{
                    "decimal":        "",
                    "emptyTable":     "No se encontraron datos en la tabla",
                    "info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered":   "(filtrados de un total _MAX_ registros)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encrontraron coincidencias",
                    "paginate": {
                        "first":      "Primero",
                        "last":       "Ultimo",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": Activar para ordenar ascendentemente",
                        "sortDescending": ": Activar para ordenar descendentemente"
                    },
                    "buttons": {
                        "colvis": "Columnas",
                        "copy": "Copiar",
                        "print": "Imprimir"
                    }
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function ( i ) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
                    };
                    // Total en todas las paginas
                    total = api.column( 3 ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                    // Total en la pagina actual
                    pageTotal = api.column(3,{ page: 'current'} ).data().reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                    // actualzar total en el pie de tabla
                    $( api.column( 3 ).footer() ).html('$'+pageTotal +'<p style="color: #0c199c">'+' ( $'+ total+' )'+'</p>');
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'POA - AREA',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'POA - AREA',
                        message: 'Presupuesto asignado',
                        orientation: 'portrait',
                        pageSize: 'letter',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ],
                columnDefs: [ {
//                    targets: -1,
                    visible: false
                } ]

            });//dataTables

            table.buttons().container().appendTo( '#botones_imprimir' );

            $("#area_table").fadeIn();


            $('#area_table .search-filter').each( function () {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="'+title+'" />' );
            } );

            table.columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that.search( this.value ).draw();
                    }
                } );
            } );

        });

        $(document).on('click', '.panel-heading .btn-collapse', function (e) {
            var $this = $(this);
            if (!$this.hasClass('panel-collapsed')) {
                $this.addClass('panel-collapsed');
                $this.find('i').removeClass('fa-minus').addClass('fa-plus');
            } else {
                $this.removeClass('panel-collapsed');
                $this.find('i').removeClass('fa-plus').addClass('fa-minus');
            }
        });


    </script>
@endsection