<html>
<body>
<div>
    <h3><b>Ud debe saber que la Solicitud de Inclusión PAC #{{sprintf("%'.04d",$incpac->id)}} ha sido aprobada </b></h3>
    <hr>

    <div>
        <h5>
            Para descargar la misma dirigirse al <a href="{{route('admin.pacs.index')}}">Sistema Gestion del POA</a>, en el menú PAC, opción procesos
        </h5>
    </div>

    <div>
        <h4>Información de referencia del PAC</h4>
        <div>
            <p>
                Partida Presupuestaria: {{$incpac->cod_item}}<br>
                CPC: {{$incpac->cpc}}<br>
                Tipo Compra: {{$incpac->tipo_compra}}<br>
                Detalle: {{$incpac->concepto}}<br>
                PAC inicial (Presupuesto sin iva): {{$incpac->presupuesto}}<br>
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