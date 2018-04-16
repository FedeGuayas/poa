@if (count($area_item)>0)
    <table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table">
        <thead>
        <th>MES</th>
        <th>PRESUPUESTO</th>
        <th>ACCION</th>
        </thead>
        <tbody>

        @foreach($area_item as $item)
            <tr>
                <td>{{$item->month}}</td>
                <td>$ {{number_format($item->monto,2,'.',' ')}}</td>
                <td>
                    @permission('planifica-poa')
                    {{--Verificar condiciones para poder eliminra la inclusion--}}
                    <a href="#!" class="btn-xs btn-danger delete tip" data-placement="top" title="Eliminar"
                       data-id="{{$item->id}}"><i class="fa fa-trash-o"></i></a>
                    @endpermission
                </td>
            </tr>
        @endforeach
        </tbody>
    </table><!--end table-responsive-->

@else
    <h4>No hay Inclusión planificada para el presente item en el área seleccionada</h4>
@endif
