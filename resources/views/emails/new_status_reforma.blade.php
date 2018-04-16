<html>
    <body>
    <div>
        <h4>Reforma {{$accion}}</h4>
        <hr>

        <p>
            Reforma por valor de $ {{number_format($reforma->monto_orig,2,'.',',')}} de la
            actividad {{$reforma->area_item->item->cod_actividad}} item {{$reforma->area_item->item->cod_item}}
            "{{$reforma->area_item->item->item}}" del mes de {{strtolower($reforma->area_item->month->month)}}
        </p>

        <div>
            <p>
                Este correo fue generado por el sistema, favor no contestarlo
            </p>
        </div>

    </div>
    </body>
</html>