<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    {{--<link rel="stylesheet" href="css/pdf.css">--}}
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<style>
    body{
        margin-left: 60px;
        margin-right: 60px;
    }
    table {
        width: 80%;
        border: 1px solid #000;
        margin-right:auto;
        margin-left:auto;
        margin-top: auto;
        margin-bottom: 50px;
    }
    table th, td {
        width: 25%;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #000;
        padding: 0.5em;
    }
    .encabezado{
        margin-top: 200px;
    }
    .titulo{
        font-size: 20px;
        text-align: center;
        font-weight: bold;
    }
    table, .fecha, .detalle{
        margin-top: 60px;
    }
    .firma {
        position: absolute;
        bottom: 100px;
    }
</style>
<body>

<div class="encabezado">
    <p class="titulo">CERTIFICACIÓN DEL PLAN ANUAL DE CONTRATACIONES <br>
        NO. FDG-ADFIN-CPAC-{{sprintf("%'.05d",$cpac->id)}}-{{$fecha_actual->year}}
    </p>
</div>

<p class="fecha">
    Guayaquil, {{$fecha_actual->day.' de '.$month.' de '.$fecha_actual->year }}
</p>

<p class="detalle">
    Mediante la presente se emite la CERTIFICIÓN DE DISPONIBILIDAD EN EL PLAN ANUAL DE CONTRATACIONES {{$fecha_actual->year}} para {{$cpac->pac->concepto}} DE LA FEDERACIÓN DEPORTIVA DEL GUAYAS, para lo cual se detalla:
</p>

    <table>
        <thead>
        <tr>
            <th>Partida Presupuestaria</th>
            <th>Código CPC</th>
            <th>Monto</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{$cpac->partida}}</td>
            <td>{{$cpac->cpc}}</td>
            <td>$ {{number_format($cpac->monto,2,',','.') }}</td>
        </tr>
        </tbody>
    </table>

<p>
    Sin otro particular
</p>

<div class="firma">
    <P>
        Ing. Omar Jacome Ortega <br>
        <b>Administrador Financiero-Tesorero <br>
            Federación Deportiva del Guayas</b>
    </P>
    <br><br>
    <p>
        Elaborado por: Domenique Ulloa
    </p>
</div>




</body>
</html>


