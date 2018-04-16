<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        @page {
            margin: 100px 60px;
            text-align: justify
        }

        #header {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            margin-bottom: 40px;
            text-align: center;
        }

        #logo {
            text-align: center;
            width: 50%;
            position: absolute;
            top: -30px;
            margin-top: -100px;
            margin-left: 150px;
        }

        #content {
            position: relative;
            margin-top: 120px
        }

        #footer {
            position: fixed;
            left: 0;
            bottom: -120px;
            right: 0;
            height: 20px;
            text-align: right;
            font-size: 10px
        }

        #footer .page:after {
            content: counter(page);
        }

        .firma {
            position: absolute;
            margin-bottom: 50px;
            margin-top: 50px;
        }


    </style>
</head>
<body>


<div id="header">
    <strong>INFORME TÉCNICO DE {{strtoupper($reforma->tipo_informe)}} AL PLAN OPERATIVO
        ANUAL {{$fecha_actual->year}}
        <br>
        FDG-REF-POA{{$fecha_actual->year.'-'.$reforma->cod_informe.'-'.($reforma->cod_informe=='MIN' ? sprintf("%'.03d",$reforma->num_min) : sprintf("%'.03d",$reforma->num_modif)) }}
    </strong>
</div>

<div id="content">
    <div id="logo">
        <img src="images/fdg-logo.png" alt="" style="width: 150px; padding: 5px;">
    </div>


    <table width="100%" border="0">
        <tr>
            <td><b>PARA:</b></td>
            <td><b>ING. BLANCA SILVA</b> / DIRECTORA DE PLANIFICACIÓN Y CONTROL DE GESTIÓN</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><b>DE:</b></td>
            <td><b>{{strtoupper($jefe_area->tratamiento)}}.{{$jefe_area->nombres.' '.$jefe_area->apellidos}}</b>
                / {{$jefe_area->cargo}}</td>
        </tr>
        <tr>
            <td><b>FECHA:</b></td>
            <td>{{$fecha_actual->day.' '.studly_case($month).' de '.$fecha_actual->year}}</td>
        </tr>
    </table>

    <hr>

    <p>
        Por medio de la presente solicito a usted, se autorice la {{strtolower($reforma->tipo_informe)}} modificatoria
        correspondiente a la Planificación Operativa Anual {{$fecha_actual->year}}, de acuerdo al siguiente detalle:
    </p>

    <b>Modificación al POA {{$reforma->cod_informe=='MIN' ? 'entre actividades' : 'misma actividad'}}</b>
    <ul>
        @foreach($reforma->pac_destino as $pd )
            <li>
                {{$reforma->nota}} ( {{$reforma->area_item->item->cod_item}} {{$reforma->area_item->item->item}}
                - {{$reforma->area_item->item->cod_actividad}}
                - {{$reforma->area_item->item->actividad_programa->actividad->actividad}} ), {{$pd->justificativo}}
                ( {{$pd->pac->cod_item}} {{$pd->pac->item}}
                - {{$pd->pac->area_item->item->cod_actividad}}
                - {{$pd->pac->area_item->item->actividad_programa->actividad->actividad}} )
                {{--{{number_format($reforma->monto_orig,2,'.',',')}} --}}
            </li>
        @endforeach
    </ul>
    <b>Análisis de afectación de metas </b>
    <p>
        Las modificaciones solicitadas en las actividades que conforman la Planificación Operativa
        Anual {{$fecha_actual->year}}, no afectarán a las metas planteadas ya que las mismas se cumplirán a medida que
        se ejecute el presupuesto.
    </p>

    <b>Base Legal</b>
    <p>
        Según art. 74 del Reglamento Genereal a la Ley del Deporte, Educación Física y Recreación, establece "De las
        modificaciónes al POA.- Las organizaciones deportivas podrán, en función de sus necesidades debidamente
        justificadas, modificar su plan operativo anual aprobado por el Ministerio Sectorial de conformidad a las
        disposiciones por este último".
    </p>
    <b>Documentos Habilitantes</b>
    <ul>

        Adjunto Matriz de {{$reforma->tipo_informe}} POA {{$fecha_actual->year}}

    </ul>

</div>

<div class="firma">
    <table style="border:1px solid;" width="100%">
        <thead>
        <tr>
            <th width="50%" style="padding: 5px 5px 30px 5px;">Elaborado Por:</th>
            <th style="padding: 5px 5px 30px 5px;">Solicitado Por:</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="padding-left: 5px; text-transform: capitalize">Nombre: {{$reforma->user->worker->tratamiento}}
                . {{$reforma->user->worker->getFullName()}}</td>
            <td style="padding-left: 5px; text-transform: capitalize">Nombre: {{$jefe_area->tratamiento}}
                . {{$jefe_area->nombres.' '.$jefe_area->apellidos}} </td>
        </tr>
        <tr>
            <td style="padding-left: 5px; text-transform: capitalize">Cargo: {{$reforma->user->worker->cargo}}.</td>
            <td style="padding-left: 5px; text-transform: capitalize">Cargo: {{$jefe_area->cargo}}</td>
        </tr>
        </tbody>

    </table>

</div>


</body>
</html>


