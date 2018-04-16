<div class="panel panel-info">
    <div class="panel-heading clearfix">Resumen
        <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
           aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
    </div>
    <div class="panel-body collapse in" id="resumen">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <th>Programa</th>
                <th>Actividad</th>
                </thead>
                <tbody>
                <tr>
                    <td>{{$reforma->programa}} </td>
                    <td>{{$reforma->actividad}}</td>
                </tr>
                <tr>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-bordered table-hover">
                            <thead>
                            <th>Item</th>
                            <th>Codigo</th>
                            <th>Grupo G.</th>
                            <th>Monto</th>
                            <th>Mes</th>
                            <th>Estado</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td> {{$reforma->item}}</td>
                                <td> {{$reforma->cod_programa.'-'.$reforma->cod_actividad.'-'.$reforma->cod_item}}</td>
                                <td> {{$reforma->grupo_gasto}}</td>
                                <td>$ {{$reforma->monto_orig}}</td>
                                <td> {{$reforma->mes}}</td>
                                <td>
                                    @if ($reforma->estado==\App\Reforma::REFORMA_PENDIENTE)
                                        <span class="label label-warning">Pendiente</span>
                                    @else
                                        <span class="label label-success">Aprobada</span>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </tr>
                </tbody>
            </table>
        </div>
    </div>{{--panel-body--}}
</div>{{--panel-info resumen--}}

<div class="panel panel-success">
    <div class="panel-heading clearfix">Detalle
        <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#detalle"
           aria-expanded="false" aria-controls="detalle"><i class="fa fa-minus"></i></a>
    </div>
    <div class="panel-body collapse in" id="detalle">
        <div class="table-responsive">
            <table class="table table-bordered" style="font-size: 11px;">
                <thead>
                <th>Origen</th>
                <th>Destino</th>
                </thead>
                <tbody>
                <tr>
                    {{--origen--}}
                    <td>
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover">
                                <thead>
                                <th>Proceso</th>
                                <th>Responsable</th>
                                <th>Monto</th>
                                </thead>
                                <tbody>
                                @foreach($detalles_o as $do)
                                    <tr>
                                        <td>{{$do->concepto}}</td>
                                        <td>{{$do->nombres.' '.$do->apellidos}}</td>
                                        <td>$ {{$do->valor_orig}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                    {{--destino--}}
                    <td>
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover">
                                <thead>
                                <th>Código</th>
                                <th>Item</th>
                                <th>Responsable</th>
                                <th>Proceso</th>
                                <th>Monto</th>
                                <th>Mes</th>
                                </thead>
                                <tbody>
                                @foreach($detalles_d as $dd)
                                    <tr>
                                        <td> {{$dd->cod_programa.'-'.$dd->cod_actividad.'-'.$dd->cod_item}}</td>
                                        <td>{{$dd->item}}</td>
                                        <td>{{$dd->nombres.' '.$dd->apellidos}}</td>
                                        <td>{{$dd->concepto}}</td>
                                        <td>$ {{$dd->valor_dest}}</td>
                                        <td>{{$dd->mes}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>{{--panel-body--}}
</div>{{--panel-success detalle--}}

