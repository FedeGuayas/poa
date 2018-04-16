<html>
<body>
<div>
    <h3><b>La Certificación Presupuestaria con código {{$cpresup->cod_cert_presup}} ha sido subida al sistema. Puede continuar con el proceso</b></h3>
    <hr>

    <div>
        <h5>
            Si desea descargar la misma dirigirse al <a href="{{route('admin.pacs.index')}}">Sistema Gestion del POA</a>, en el menú PAC, opción Procesos, en la tabla que se muestra busque el proceso al que se hace referencia mediante los filtros habilitado para tal efecto
        </h5>

    </div>

    <div>
        <h4>Información de referencia del PAC</h4>
        <div>
            <p>
                Partida Presupuestaria: {{$pac->cod_item}}<br>
                Código: {{$pac->area_item->item->cod_programa.'-'.$pac->area_item->item->cod_actividad.'-'.$pac->area_item->item->cod_item}}<br>
                Item: {{$pac->item}}

            </p>

        </div>
    </div>

    <div>
        <p>
            Este correo fue generado por el sistema, favor no contestarlo.
        </p>
    </div>

</div>
</body>
</html>