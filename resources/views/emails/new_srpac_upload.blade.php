<html>
<body>
<div>
    <h3><b>La Solicitud de Reforma PAC #{{sprintf("%'.04d",$srpac->id)}} para cambio en "{{$srpac->notas}}" ha sido aprobada </b></h3>
    <hr>

    <div>
        <h5>
            Para descargar la misma dirigirse al <a href="{{route('admin.pacs.index')}}">Sistema Gestion del POA</a>, en el menú PAC, opción procesos
        </h5>

    </div>

    <div>
        <h4>Información de referencia del PAC INICIAL</h4>
        <div>
            <p>
                Partida Presupuestaria (Código): {{$srpac->cod_item}}<br>
                Detalle del producto (Concepto): {{$srpac->concepto}}<br>
                PAC inicial (Presupuesto sin iva): {{$srpac->presupuesto}}<br>
            </p>

        </div>
    </div>

    <div>
        <p>
            Este correo es generado por el sistema, favor no contestarlo
        </p>
    </div>

</div>
</body>
</html>