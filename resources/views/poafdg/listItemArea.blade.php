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
                    <a href="#!" class="btn-xs btn-success tip" data-placement="top" title="Editar" data-toggle="modal"
                       data-target="#edit-plan" onclick="mostrarEdit({{$item->id}})"><i class="fa fa-pencil"></i></a>
                    <a href="#!" class="btn-xs btn-danger delete tip" data-placement="top" title="Eliminar"
                       data-id="{{$item->id}}"><i class="fa fa-trash-o"></i></a>
                    @endpermission
                </td>
            </tr>
        @endforeach
        </tbody>
    </table><!--end table-responsive-->
    <label for="Total" class="btn btn-primary">Total <span class="badge"> $ {{number_format($total,2,'.',' ')}}</span></label>
    @include('poafdg.edit_plan')

    {!! Form::open(['route'=>['admin.poa.destroy',':ID'],'method'=>'DELETE','id'=>'form-delete']) !!}
    {!! Form::close() !!}
@else
    <h4>No hay presupuesto planificado para el presente item en el área seleccionada</h4>
@endif
