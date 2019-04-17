<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        @page { margin: 200px 60px; }
        #header { position: fixed; top:-60px; left:0; right:0;height: 40px; text-align: right; }
        .firma {  position: fixed;  bottom:120px; }

    </style>
</head>
<body>

<div id="header">
    <strong>Memorando Nro. FDG-{{$pac->area_item->area->cod_area. '-'.'IP'.'-'. $fecha_actual->year. '-'. sprintf("%'.04d",$inclusionpac->id)  }}</strong>
    <p>Guayaquil, {{$fecha_actual->day.' de '.$month.' de '.$fecha_actual->year }}</p>
</div>

<div id="content">

    <div id="pagina1">
        <table width="50%" border="0" style="margin-top: 20px; margin-bottom: 30px;">
            <tr>
                <td><b>PARA:</b></td>
                <td>Gerente Organismo Deportivo: Eco. Bella Corina González Torres</td>
            </tr>
            <tr>
                <td></td>
                <td><b>Interventor</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
            <tr>
                <td><b>ASUNTO:</b></td>
                <td > Solicitud de Inclusión PAC.</td>
            </tr>
        </table>

        <p>De mi consideración:</p>

        <p>Por medio de la presente solicito a usted autorice la inclusión  en el Plan Anual de Contratación (PAC) de año {{$fecha_actual->year}} el proceso que detallaré a continuación.</p>

        <table  width="100%"  border="1" style="margin-top: 20px; margin-bottom: 20px;">
            <thead>
            <tr>
                <th width="110px;">PARTIDA PRESUPUESTARIA/ CUENTA CONTABLE</th>
                <th width="100px;">CÓDIGO/ CATEGORíA CPC</th>
                <th width="100px;">TIPO COMPRA ( Bien, obra, servicio o consultoría )</th>
                <th >DETALLE DEL PRODUCTO ( Descripción de la contratación )</th>
                <th width="100px;">PAC INICIAL</th>
            </tr>
            </thead>
            <tbody>
            <tr style="text-align: center">
                <td>{{$inclusionpac->cod_item}}</td>
                <td>{{$inclusionpac->cpc}}</td>
                <td>{{$inclusionpac->tipo_compra}}</td>
                <td style="text-align: justify">{{$inclusionpac->concepto}}</td>
                <td>$ {{number_format($inclusionpac->presupuesto,2,'.',' ')}}</td>
            </tr>
            </tbody>
        </table>

        <p> Con sentimientos de distinguida consideración</p>

        <div class="firma">
            <p>Atentamente</p>
            <p><b>DEPORTE Y DISCIPLINA</b>
                <br><br><br><br>
            <p style="text-transform: capitalize">{{$jefe_area->tratamiento}}. {{$jefe_area->nombres. ' '. $jefe_area->apellidos}}</p>
            <p><b>{{$jefe_area->cargo}}</b></p>
        </div>
    </div>

</div>


</body>
</html>


