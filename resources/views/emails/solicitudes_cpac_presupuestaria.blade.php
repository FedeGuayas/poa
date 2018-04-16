<html>
<body>
<div>
    <h3><b>Estimada(s) por este medio se les solicita emitir certificaciones Presupuestaria y/o CPAC según corresponda.
            Se detalla la misma a continuación</b></h3>
    <hr>

    <div>
        <h5>

        </h5>
    </div>

    <div>
        <h4>Información de referencia del PAC</h4>
        <div>
            <p>
                Información POA del Proceso (programa-actividad-partida)
                : {{$codigos->cod_programa.'-'.$codigos->cod_actividad.'-'.$codigos->cod_item}}<br>
                Item: {{$codigos->item}}<br>
                Proceso: {{$pac->concepto}}<br>
                Monto referencial: (sin inc. IVA): $ {{$monto}} mas IVA<br>
                Observaciones: {{strtoupper($notas)}}
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