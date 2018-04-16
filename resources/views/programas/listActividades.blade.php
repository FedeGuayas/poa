{!! Form::model($programa,['route'=>['asociarActividades', $programa->id], 'method'=>'post','id'=>'form-vincular']) !!}
{!! Form::hidden('programa_id',$programa->id,['id'=>'pro_id']) !!}
    <div class="row">
        <div class="form-inline col-xs-12">
            <div class="form-group">
                {!! Form::label('cod_programa','Código') !!}
                {!! Form::text('cod_programa',null,['class'=>'form-control','disabled','style'=>'width: 60px;']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('programa','Programa') !!}
                {!! Form::text('programa',null,['class'=>'form-control','style'=>' width:600px', 'disabled']) !!}
            </div>
        </div>
    </div>

<hr>
<table class="table table-striped table-bordered table-condensed table-hover highlight responsive-table" id="table_actividades">
    <thead>
    <th>Cod</th>
    <th>Actividad</th>
    <th>
        {!! Form::checkbox('all_actividad',null,false,['id'=>'all_actividad']) !!}
        {!! Form::label('all_actividad','Todas') !!}
    </th>
    </thead>
    @foreach ($actividades as $actividad )
        <tr>
            <td>
                {{$actividad->cod_actividad}}
            </td>
            <td>
                {{$actividad->actividad}}
            </td>
            <td>
                {!! Form::checkbox('actividads[]',$actividad->id,null,['id'=>$actividad->id]) !!}
                {!! Form::label($actividad->id,$actividad->cod_actividad) !!}
            </td>
        </tr>
    @endforeach
</table><!--end table-responsive-->
{!! Form::close() !!}



