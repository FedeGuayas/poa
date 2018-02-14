@if (count($extras)>0)
<table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table">
    <thead>
    @foreach($extras as $ex)
    <th>{{$ex->mes}}</th>
    @endforeach
    <th>TOTAL</th>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    <tr>
        @foreach($extras as $ex)
            <td>$ {{number_format($ex->monto,2,'.',' ')}}</td>
        @endforeach
            <th> $ {{number_format($total,2,'.',' ')}}</th>
    </tr>
    </tbody>
</table><!--end table-responsive-->
@else
    <h4>No existen ingresos extras</h4>
@endif
