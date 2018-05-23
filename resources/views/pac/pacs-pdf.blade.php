<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link rel="stylesheet" href="css/pdf.css">
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>

<div class="header">
    <div style="text-align: center; position: relative">
        <p>
            <span><b>Procesos-Areas</b></span> <br>
            <span><b>{{$area->area}}</b></span>
        </p>
    </div>
</div>


<div class="content">
    {{--<table border="1" style=" width: 100%">--}}
        <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"  cellspacing="0" style="display: none;">
            <thead>
            <th style="width: 100px">Código</th>
            <th>Presupuesto</th>
            <th>Ejecutado</th>
            <th>Devengado</th>
            <th>Disponible</th>
            <th>Procedimiento</th>
            <th>Concepto</th>
            <th style="width: 12px">Mes</th>
            <th style="width: 12px">Responsable</th>
            </thead>
            <tfoot>
            <tr>
                <th>Código</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Mes</th>
                <th>Responsable</th>
            </tr>
            </tfoot>
            <tbody>
            @foreach($pacs as $pac)
                <tr>
                    <td>{{$pac->cod_programa.'-'.$pac->cod_actividad.'-'.$pac->cod_item}}</td>
                    <td>$ {{$pac->presupuesto}}</td>
                    <td>$ {{$pac->comprometido}} </td>
                    <td>$ {{$pac->devengado}}</td>
                    <td>$ {{$pac->disponible}}</td>
                    <td>{{$pac->procedimiento}}</td>
                    <td>{{$pac->concepto}}</td>
                    <td>{{$pac->mes}}</td>
                    <td>{{$pac->nombres}} {{$pac->apellidos}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

</div>


<div class="footer">

</div>


</body>
</html>


