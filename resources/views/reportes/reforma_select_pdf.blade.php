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
    <img alt="LOGO" src="images/ministerio.png" style="width: 100px; position: absolute; top:  30px; left: 20px;"/>
    <div style="position: absolute; top:  80px; left: 20px;">
        <table align="left" border="1" cellpadding="5" cellspacing="10"
               style="width:400px; text-align : center; font-size: 11px;">
            <tr>
                <td height="30px">Nombre del Organismo Deportivo:</td>
                <td>FEDERACIÓN DEPORTIVA DEL GUAYAS</td>
            </tr>
            <tr>
                <td height="30px">Modificación del POA</td>
                <td>$ {{$total_reforma}}</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; position: relative">
        <p>
            <span><b>MINISTERIO DEL DEPORTE</b></span> <br>
            <span><b>MATRIZ DE MODIFICACIÓN DEL PLAN OPERATIVO ANUAL {{\Carbon\Carbon::now()->year}} ORGANISMOS DEPORTIVOS</b></span>
        </p>
    </div>
</div>


<div class="content">
    <table border="1" style=" width: 100%">
        <thead>
        <tr>
            <th colspan="7" style="text-align: center; background-color: rgb(141,180,226)">Origen</th>
            <th colspan="7" style="text-align: center; background-color: rgb(196,215,155)">Destino</th>
        </tr>
        <tr style="background-color: rgb(204,192,218);">
            <th style="width: 20px;">No.</th>
            <th>Programa</th>
            <th>Número de Actividad</th>
            <th>Código Ítem Presupuestario</th>
            <th>Nombre del ítem Presupuestario</th>
            <th>Mes Programado</th>
            <th>Monto / Disminución</th>

            <th>No.</th>
            <th>Programa</th>
            <th>Número de Actividad</th>
            <th>Código Ítem Presupuestario</th>
            <th>Nombre del ítem Presupuestario</th>
            <th>Mes Programado</th>
            <th>Monto / Incremento</th>
        </tr>
        </thead>
        <tbody>

            @foreach ($todas as $key=>$dd)
                    <tr >
                        {{--origen--}}
                        <td style="text-align: center">{{++$key}}</td>
                        <td>{{$dd->programa_o}}</td>
                        <td>{{sprintf("%'.03d",$dd->cod_actividad_o).' '.$dd->actividad_o}}</td>
                        <td>{{$dd->cod_item_o}}</td>
                        <td>{{$dd->item_o}}</td>
                        <td>{{$dd->mes_o}}</td>
                        <td>$ {{$dd->valor_dest}}</td>

                        {{--destino--}}
                        <td style="text-align: center">{{$key}}</td>
                        <td>{{$dd->programa}}</td>
                        <td>{{sprintf("%'.03d",$dd->cod_actividad).' '.$dd->actividad}}</td>
                        <td>{{$dd->cod_item}}</td>
                        <td>{{$dd->item}}</td>
                        <td>{{$dd->mes}}</td>
                        <td>$ {{$dd->valor_dest}}</td>
                    </tr>

            @endforeach

        </tbody>
        <tr>
            <th colspan="6" style="text-align: center; background-color: rgb(141,180,226)">TOTAL</th>
            <th style="text-align: center; background-color: rgb(141,180,226)">$ {{$total_reforma}}</th>
            <th colspan="6" style="text-align: center; background-color: rgb(196,215,155)">TOTAL</th>
            <th style="text-align: center; background-color: rgb(196,215,155)">$ {{$total_reforma}}</th>
        </tr>
    </table>


    {{--<table border="1" style=" width: 100%">--}}
        {{--<thead>--}}
            {{--<tr>--}}
                {{--<th colspan="7" style="text-align: center; background-color: rgb(141,180,226)">Origen</th>--}}
                {{--<th colspan="7" style="text-align: center; background-color: rgb(196,215,155)">Destino</th>--}}
            {{--</tr>--}}
            {{--<tr style="background-color: rgb(204,192,218)">--}}
                {{--<th style="width: 20px;">No.</th>--}}
                {{--<th>Programa</th>--}}
                {{--<th>Número de Actividad</th>--}}
                {{--<th>Código Ítem Presupuestario</th>--}}
                {{--<th>Nombre del ítem Presupuestario</th>--}}
                {{--<th>Mes Programado</th>--}}
                {{--<th>Monto / Disminución</th>--}}

                {{--<th>No.</th>--}}
                {{--<th>Programa</th>--}}
                {{--<th>Número de Actividad</th>--}}
                {{--<th>Código Ítem Presupuestario</th>--}}
                {{--<th>Nombre del ítem Presupuestario</th>--}}
                {{--<th>Mes Programado</th>--}}
                {{--<th>Monto / Incremento</th>--}}
            {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}
        {{--@if(count($detalles_d)>count($detalles_o))--}}
            {{--@foreach ($detalles_d as $dd)--}}

                {{--@foreach ($detalles_o as $do)--}}
                    {{--<tr>--}}
                    {{--origen--}}

                    {{--<td>{{$do->cod_programa}}</td>--}}
                    {{--<td>{{$do->programa}}</td>--}}
                    {{--<td>{{sprintf("%'.03d",$do->cod_actividad).' '.$do->actividad}}</td>--}}
                    {{--<td>{{$do->cod_item}}</td>--}}
                    {{--<td>{{$do->item}}</td>--}}
                    {{--<td>{{$do->mes}}</td>--}}
                    {{--<td>$ {{$dd->valor_dest}}</td>--}}

                    {{--destino--}}
                    {{--<td>{{$dd->cod_programa}}</td>--}}
                    {{--<td>{{$dd->programa}}</td>--}}
                    {{--<td>{{sprintf("%'.03d",$dd->cod_actividad).' '.$dd->actividad}}</td>--}}
                    {{--<td>{{$dd->cod_item}}</td>--}}
                    {{--<td>{{$dd->item}}</td>--}}
                    {{--<td>{{$dd->mes}}</td>--}}
                    {{--<td>$ {{$dd->valor_dest}}</td>--}}
                    {{--</tr>--}}
                {{--@endforeach--}}

            {{--@endforeach--}}
            {{--@elseif (count($detalles_d)<count($detalles_o))--}}
                {{--@foreach ($detalles_o as $do)--}}
                    {{--@foreach ($detalles_d as $dd)--}}
                        {{--<tr>--}}
                            {{--origen--}}

                            {{--<td>{{$do->cod_programa}}</td>--}}
                            {{--<td>{{$do->programa}}</td>--}}
                            {{--<td>{{sprintf("%'.03d",$do->cod_actividad).' '.$do->actividad}}</td>--}}
                            {{--<td>{{$do->cod_item}}</td>--}}
                            {{--<td>{{$do->item}}</td>--}}
                            {{--<td>{{$do->mes}}</td>--}}
                            {{--<td>$ {{$do->valor_orig}}</td>--}}

                            {{--destino--}}
                            {{--<td>{{$dd->cod_programa}}</td>--}}
                            {{--<td>{{$dd->programa}}</td>--}}
                            {{--<td>{{sprintf("%'.03d",$dd->cod_actividad).' '.$dd->actividad}}</td>--}}
                            {{--<td>{{$dd->cod_item}}</td>--}}
                            {{--<td>{{$dd->item}}</td>--}}
                            {{--<td>{{$dd->mes}}</td>--}}
                            {{--<td>$ {{$do->valor_orig}}</td>--}}
                        {{--</tr>--}}
                    {{--@endforeach--}}
                {{--@endforeach--}}
            {{--@else--}}
                {{--<tr>--}}
                    {{--@foreach ($detalles_o as $do)--}}
                        {{--origen--}}

                        {{--<td>{{$do->cod_programa}}</td>--}}
                        {{--<td>{{$do->programa}}</td>--}}
                        {{--<td>{{sprintf("%'.03d",$do->cod_actividad).' '.$do->actividad}}</td>--}}
                        {{--<td>{{$do->cod_item}}</td>--}}
                        {{--<td>{{$do->item}}</td>--}}
                        {{--<td>{{$do->mes}}</td>--}}
                        {{--<td>$ {{$do->valor_orig}}</td>--}}
                    {{--@endforeach--}}
                    {{--@foreach ($detalles_d as $dd)--}}
                        {{--destino--}}
                        {{--<td>{{$dd->cod_programa}}</td>--}}
                        {{--<td>{{$dd->programa}}</td>--}}
                        {{--<td>{{sprintf("%'.03d",$dd->cod_actividad).' '.$dd->actividad}}</td>--}}
                        {{--<td>{{$dd->cod_item}}</td>--}}
                        {{--<td>{{$dd->item}}</td>--}}
                        {{--<td>{{$dd->mes}}</td>--}}
                        {{--<td>$ {{$dd->valor_dest}}</td>--}}
                    {{--@endforeach--}}
                {{--</tr>--}}
        {{--@endif--}}
        {{--</tbody>--}}
        {{--<tr>--}}
            {{--<th colspan="6" style="text-align: center; background-color: rgb(141,180,226)">TOTAL</th>--}}
            {{--<th style="text-align: center; background-color: rgb(141,180,226)">$ {{$reforma->monto_orig}}</th>--}}
            {{--<th colspan="6" style="text-align: center; background-color: rgb(196,215,155)">TOTAL</th>--}}
            {{--<th style="text-align: center; background-color: rgb(196,215,155)">$ {{$reforma->monto_orig}}</th>--}}
        {{--</tr>--}}
    {{--</table>--}}

    {{--<div style="position: absolute; top:  42px; left: 5px;">--}}
    {{--<table border="1" style="font-size: 9px; width: 49%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th>No.</th>--}}
            {{--<th>Programa</th>--}}
            {{--<th>Número de Actividad</th>--}}
            {{--<th>Código Ítem Presupuestario</th>--}}
            {{--<th>Nombre del ítem Presupuestario</th>--}}
            {{--<th>Mes Programado</th>--}}
            {{--<th>Monto / Disminución</th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}
        {{--@foreach($detalles_o as $do)--}}
            {{--<tr>--}}
                {{--<td style="width: 20px;">{{$do->cod_programa}}</td>--}}
                {{--<td>{{$do->programa}}</td>--}}
                {{--<td>{{sprintf("%'.03d",$do->cod_actividad).' '.$do->actividad}}</td>--}}
                {{--<td>{{$do->cod_item}}</td>--}}
                {{--<td>{{$do->item}}</td>--}}
                {{--<td>{{$do->mes}}</td>--}}
                {{--<td>$ {{$do->valor_orig}}</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
        {{--</tbody>--}}
    {{--</table>--}}
    {{--</div>--}}

    {{--<div style="position: absolute; top:  42px; left: 540px;">--}}
    {{--<table border="1" style="font-size: 9px; width: 49%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th>No.</th>--}}
            {{--<th>Programa</th>--}}
            {{--<th>Número de Actividad</th>--}}
            {{--<th>Código Ítem Presupuestario</th>--}}
            {{--<th>Nombre del ítem Presupuestario</th>--}}
            {{--<th>Mes Programado</th>--}}
            {{--<th>Monto / Incremento</th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}
        {{--@foreach($detalles_d as $dd)--}}
            {{--<tr>--}}
                {{--<td>{{$dd->cod_programa}}</td>--}}
                {{--<td>{{$dd->programa}}</td>--}}
                {{--<td>{{sprintf("%'.03d",$dd->cod_actividad).' '.$dd->actividad}}</td>--}}
                {{--<td>{{$dd->cod_item}}</td>--}}
                {{--<td>{{$dd->item}}</td>--}}
                {{--<td>{{$dd->mes}}</td>--}}
                {{--<td>$ {{$dd->valor_dest}}</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
        {{--</tbody>--}}
    {{--</table>--}}
    {{--</div>--}}

</div>


<div class="footer">
    <table>
        <tr>
            <td>
                <p align="left">
                   Elaborado por:
                </p>

                <p align="left">
                    _____________________________________
                </p>
                <p align="left" style="text-transform: capitalize">
                    Nombre: {{Auth::user()->worker->tratamiento.'. '.Auth::user()->worker->getFullName()}}<br>
                    Cargo: {{Auth::user()->worker->cargo}}<br>
                    CI: {{Auth::user()->worker->num_doc}}
                </p>
            </td>
            <td>
                <p align="left">
                    Autorizado por:
                </p>

                <p align="left">
                    _____________________________________
                </p>
                <p align="left" style="text-transform: capitalize">
                    Nombre: {{$jefe_area->tratamiento.'. '.$jefe_area->getFullName()}} <br>
                    Cargo: {{$jefe_area->cargo}}<br>
                    CI: {{$jefe_area->num_doc}}
                </p>
            </td>
        </tr>
    </table>
</div>


</body>
</html>


