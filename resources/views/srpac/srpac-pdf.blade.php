<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        @page { margin: 200px 60px; }
        #header { position: fixed; top:-60px; left:0; right:0;height: 40px; text-align: right; }
        #footer { position: fixed; left: 0; bottom: -120px; right: 0; height: 20px; text-align: right; font-size: 10px }
        #footer .page:after { content: counter(page); }
        .firma {  position: absolute;  bottom: 100px;  }

    </style>
</head>
<body>

<div id="header">
    <strong>Memorando Nro. FDG-{{$pac->area_item->area->cod_area. '-'. $fecha_actual->year. '-'. sprintf("%'.04d",$srpac->id)  }}</strong>
    <p>Guayaquil, {{$fecha_actual->day.' de '.$month.' de '.$fecha_actual->year }}</p>
</div>
<div id="footer">
    <p class="page">Página </p>
</div>
<div id="content">

    <div id="pagina1">
        <table width="50%" border="0">
            <tr>
                <td><b>PARA:</b></td>
                <td>Arq. Rosa Edith Rada Alprecht</td>
            </tr>
            <tr>
                <td></td>
                <td><b>Administradora</b></td>
            </tr>
        </table>

        <table width="100%" border="0" style="margin-top: 20px; margin-bottom: 20px;">
            <tr>
                <td style="vertical-align: top">
                    <b>ASUNTO:</b>
                </td>
                <td style="text-align: justify">
                    SOLICITUD REFORMA PAC PARA EL PROCESO " {{$srpac->concepto}} "
                </td>
            </tr>
        </table>

        <p>De mi consideración:</p>

        <p>Por medio del presente solicito a usted autorice la reforma en {{$srpac->notas}} del proceso que a continuación se detalla en el Plan Anual de Contratación (PAC) del año {{$fecha_actual->year}}.</p>

        <table width="100%" border="1">
            <caption><b>PAC INICIAL</b></caption>
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
                <td>{{$srpac->cod_item}}</td>
                <td>{{$srpac->cpc}}</td>
                <td>{{$srpac->tipo_compra}}</td>
                <td style="text-align: justify">{{$srpac->concepto}}</td>
                <td>$ {{number_format($srpac->presupuesto,2,'.',' ')}}</td>
            </tr>
            </tbody>
        </table>

    </div>

    <div id="pagina2" style="page-break-before: always;">

        <table width="100%" border="1" style="margin-bottom: 40px;">
            <caption><b>PAC MODIFICADO</b></caption>
            <thead>
            <tr>
                <th width="110px;">PARTIDA PRESUPUESTARIA/ CUENTA CONTABLE</th>
                <th width="100px;">CÓDIGO/ CATEGORíA CPC</th>
                <th width="100px;">TIPO COMPRA ( Bien, obra, servicio o consultoría )</th>
                <th >DETALLE DEL PRODUCTO ( Descripción de la contratación )</th>
                <th width="100px;">PAC MODIFICADO</th>
            </tr>
            </thead>
            <tbody>
            @foreach($srpac->srpac_destino as $srpac_dest)
            <tr style="text-align: center">
                <td>{{$srpac_dest->cod_item}}</td>
                <td>{{$srpac_dest->cpc}}</td>
                <td>{{$srpac_dest->tipo_compra}}</td>
                <td style="text-align: justify">{{$srpac_dest->concepto}}</td>
                <td>$ {{number_format($srpac_dest->presupuesto,2,'.',' ')}}</td>
            </tr>
                @endforeach
            </tbody>
        </table>

        <p> Con sentimientos de distinguida consideración</p>

        <div class="firma">
            <p>Atentamente</p>
            <p><b>DEPORTE Y DISCIPLINA</b>
                <br><br><br><br><br><br>
            <p style="text-transform: capitalize">{{$jefe_area->tratamiento}}. {{$jefe_area->nombres. ' '. $jefe_area->apellidos}}</p>
            <p><b>{{$jefe_area->cargo}}</b></p>
        </div>

    </div>

</div>


</body>
</html>


