<!-- Modal User Image -->
<div class="modal fade" id="searchPAC" tabindex="-1" role="dialog" aria-labelledby="searchPACTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="searchPACTitle">Buscar PAC</h5>
            </div>
            {!! Form::open() !!}
            {{--{!! Form::open(['route' =>['createReforma'], 'method'=>'POST','id'=>'reform-form']) !!}--}}
            {!! Form::hidden('fila_pac_modificado',null,['class'=>'fila_pac_modificado']) !!}

            <div class="modal-body">
                {{--<div class="form-group">--}}
                    {{--{!! Form::label('reform_type','Seleccione el tipo de reforma') !!}--}}
                    {{--{!! Form::select('reform_type',$list_reformas, null, ['class' => 'form-control','id'=>'reform_type']) !!}--}}
                {{--</div>--}}

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"
                           cellspacing="0" style="display: none; font-size: 9px;">
                        <thead>
                        <tr>
                            <th style="width: 60px">Código</th>{{--Codigo Item prog-act-item--}}
                            <th>CPC</th>
                            <th>T.Compra</th>
                            <th style="width: 60px">Mes</th>
                            <th style="width: 100px">Responsable</th>
                            <th>Concepto</th>
                            <th>Monto (-IVA)</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">partida</th>
                            <th class="search-filter">cpc</th>
                            <th class="search-filter">t_compra</th>
                            <th class="search-filter">mes</th>
                            <th class="search-filter">responsable</th>
                            <th class="search-filter">concepto</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($pacs as $p)
                            <tr>
                                <td class="valores">{{$p->cod_item}}</td>
                                <td class="valores">{{$p->cpc}}</td>
                                <td class="valores">{{$p->tipo_compra}}</td>
                                <td>{{$p->mes}}</td>
                                <td>{{$p->nombres}} {{$p->apellidos}}</td>
                                <td class="valores">{{$p->concepto}}</td>
                                <td class="valores">{{round((($p->presupuesto-$p->comprometido-$p->devengado)/1.12),2)}}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs add_pac" data-id="{{$p->id}}" data-dismiss="modal">
                                        <i class="fa fa-check-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
</div>