@extends('layouts.master')
@section('title','PAC-Procesos')

@section('breadcrumbs', Breadcrumbs::render('pac-proceso'))

@section('content')
    @include('alert.alert_json')
    @include('alert.alert')
    @include('alert.request')

    <div class="col-md-12">
        {!! Form::open(['route'=>['admin.pacs.index'],'method'=>'GET','class'=>'form_noEnter', 'id'=>'form_search']) !!}
        <div class="form-inline">
            <div class="form-group">
                {{--{!! Form::label('area','Areas') !!}--}}
                {!! Form::select('area',$list_areas,$area_select,['class'=>'form-control','placeholder'=>'Direcciones...','id'=>'area']) !!}
            </div>
            {!! Form::button('<i class="fa fa-search" aria-hidden="true"></i>',['class'=>'btn btn-primary tip','data-placement'=>'top', 'title'=>'Buscar','id'=>'buscar', 'type'=>'submit']) !!}
        </div>
        {!! Form::close() !!}

        <hr>

        <div class="panel panel-success">
            <div class="panel-heading clearfix">PAC - {{ count($area)>0 ? $area->area : "Direcciones" }}
                <a href="#!" class="btn-collapse pull-right" data-toggle="collapse" data-target="#resumen"
                   aria-expanded="false" aria-controls="resumen"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body collapse in" id="resumen">
                <div class="row">
                    <div class="container col-lg-6" id="botones_imprimir">
                        {{--{!! Form::open(['route'=>['admin.pacs.pac-pdf'],'method'=>'GET','id'=>'form_imprimir']) !!}--}}
                        {{--<a href="#!" class="btn btn-default tip" type="submit" data-placement="top" title="Imprimir" target="_blank" id="imprimir_pdf"><i class="fa fa-print"></i></a>--}}
                        {{--{!! Form::close() !!}--}}
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="pac_table"
                           cellspacing="0" style="display: none; font-size: 9px;">
                        <thead>
                        <th style="width: 60px">Código</th>
                        <th>Item</th>
                        <th>Dirección
                        </th>{{--Area del item presupuestario, coloreada si el area del responsable no es la misma, o sea si es un item compratido--}}
                        <th style="width: 60px">Mes</th>
                        <th style="width: 100px">Responsable</th>
                        <th>Procedimiento</th>
                        <th>Concepto</th>
                        <th>Presupuesto</th>
                        <th>Comprometido</th>
                        <th>Devengado</th>
                        <th>Disponible</th>
                        <th>Reformar</th>
                        <th>Inclusión</th>
                        <th>Sol.REF.PAC</th>
                        <th>Cert.PAC</th>
                        <th>Cert.Presup</th>
                        <th style="width: 65px">Acción</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="search-filter">codigo</th>
                            <th class="search-filter">item</th>
                            <th></th>
                            <th class="search-filter">mes</th>
                            <th class="search-filter">comprador</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($pacs as $pac)

                            {{--Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac),
                              o el trabajador pertenece al area que se asigno el pac y es analista o responsable-poa,
                              o  es root o administrador, mostrarlo--}}
                            @if ( ( (Auth::user()->worker_id==$pac->trabajador_id) && Auth::user()->hasRole('responsable-pac') ||
                            (Auth::user()->worker->departamento->area_id==$pac->area_trabajador && (Auth::user()->hasRole('analista') || Auth::user()->hasRole('responsable-poa'))) ) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador')))

                                <tr>
                                    <td>{{$pac->cod_programa.'-'.$pac->cod_actividad.'-'.$pac->cod_item}}</td>
                                    <td>{{$pac->item}}</td>
                                    <td>
                                        {{--verifico si presupuesto es del area del trabajador o de otro poa compartido  --}}
                                        @if ($pac->area_trabajador == $pac->aiID)
                                            {{$pac->area}}
                                        @else
                                            {{--EL dinero es compartido con trabajadores de otra area--}}
                                            <strong class="text-danger">{{$pac->area}}</strong>
                                        @endif
                                    </td>
                                    <td>{{$pac->mes}}</td>
                                    <td>{{$pac->nombres}} {{$pac->apellidos}}</td>
                                    <td>{{$pac->procedimiento}}</td>
                                    <td>{{$pac->concepto}}</td>
                                    <td>$ {{$pac->presupuesto}}</td>
                                    <td>$ {{$pac->comprometido}} </td>
                                    <td>$ {{$pac->devengado}}</td>
                                    <td>
                                        $ {{$pac->disponible}}
                                    </td>
                                    <td>
                                        $ {{$pac->liberado}}
                                    </td>
                                    <td>
                                        {{--INCLUSION--}}
                                        {{--Si es un una inclusion y es un proceso pac--}}
                                        @if($pac->inclusion==\App\Pac::PROCESO_INCLUSION_SI && $pac->proceso_pac==\App\Pac::PROCESO_PAC)
                                            {{--si tiene permisos create-inc-pac, responsables de proceso--}}
                                            @permission('create-inc-pac')
                                            {{-- Generar PDF Solicitud de inclusion pac, sino hay un archivo inclusion pac subido o esta inactivo--}}
                                            @if ( (is_null($pac->inclusion_file) ||  $pac->inclusion_file_status==\App\InclusionPac::INCLUSION_PAC_INACTIVA))
                                                <a href="{{route('create.inclusion-pac',$pac->id)}}"
                                                   class="btn btn-xs btn-danger tip" data-placement="top"
                                                   title="Solicitud Inclusión PAC">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @endif
                                            @endpermission
                                            {{-- Subir PDF Solicitud de Inclusion PAC--}}
                                            {{--Si el usuario es del area del pac y es el responsable del poa, o es root, administrador, financiero y ademas no existe archivo subido o esta inactivo--}}
                                            @if ((Auth::user()->worker->departamento->area_id==$pac->area_trabajador &&  Auth::user()->hasRole('responsable-poa')) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador') || Auth::user()->hasRole('financiero'))  && (is_null($pac->inclusion_file) || $pac->inclusion_file_status==\App\InclusionPac::INCLUSION_PAC_INACTIVA ))
                                                <a href="#subirIncPAC" data-toggle="modal" data-id="{{$pac->id}}"
                                                   data-backdrop="static" data-keyboard="false"
                                                   class="btn btn-xs btn-success tip" data-placement="top"
                                                   title="Subir Solicitud Inclusión PAC">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            {{-- Descargar PDF Solicitud de Inclusion pac si existe y esta activo--}}
                                            @if (!is_null($pac->inclusion_file) && $pac->inclusion_file_status==\App\InclusionPac::INCLUSION_PAC_ACTIVA)
                                                <a href="{{route('pac.inclusion.IncPacDownload',$pac->id)}}"
                                                   class="btn btn-xs btn-primary tip" data-placement="top"
                                                   title="Descargar Solicitud Inclusión PAC">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                        {{--FIN INCLUSION--}}
                                    </td>
                                    <td>
                                        {{--SOLICITUD DE REFORMA PAC--}}
                                        {{--Si es un proceso pac--}}
                                        @if($pac->proceso_pac==\App\Pac::PROCESO_PAC)
                                            {{--Si tiene permisos para solicitar reforma pac--}}
                                            @permission('sr-pac')
                                            {{-- Generar PDF , sino hay un archivo srpac activo y ademas no es una inclusion--}}
                                            @if ( (is_null($pac->srpac_file) ||  $pac->srpac_status==\App\Srpac::SRPAC_INACTIVA) && $pac->inclusion==\App\Pac::PROCESO_INCLUSION_NO)
                                                <a href="{{route('admin.srpacs.create',$pac->id)}}"
                                                   class="btn btn-xs btn-danger tip" data-placement="top"
                                                   title="Solicitud Reforma PAC">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @endif
                                            @endpermission
                                            {{-- Subir PDF Solicitud de reforma pac SRPAC --}}
                                            {{--Si el usuario es del area del pac y es el responsable del poa, o root, administrador, financiero y no existe archivo subido y activo y no es inclusion--}}
                                            @if ((Auth::user()->worker->departamento->area_id==$pac->area_trabajador &&  Auth::user()->hasRole('responsable-poa')) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador') || Auth::user()->hasRole('financiero'))  && (is_null($pac->srpac_file) || $pac->srpac_status==\App\Srpac::SRPAC_INACTIVA ) && $pac->inclusion==\App\Pac::PROCESO_INCLUSION_NO)
                                                <a href="#subirSRPAC" data-toggle="modal" data-id="{{$pac->id}}"
                                                   data-backdrop="static" data-keyboard="false"
                                                   class="btn btn-xs btn-success tip" data-placement="top"
                                                   title="Subir Solicitud Reforma PAC">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            {{-- Descargar PDF Solicitud de reforma pac SRPAC si existe--}}
                                            @if (!is_null($pac->srpac_file) && $pac->srpac_status==\App\Srpac::SRPAC_ACTIVA)
                                                <a href="{{route('pac.SRPACdownload',$pac->id)}}"
                                                   class="btn btn-xs btn-primary tip" data-placement="top"
                                                   title="Descargar Solicitud Reforma PAC">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                        {{--FIN SOLICITUD DE REFORMA PAC--}}
                                    </td>
                                    <td>
                                        {{--CERTIFICACION PAC (CPAC)--}}
                                        {{--Solicitar certificaciones x correo--}}
                                        @if ($pac->disponible>0)
                                            <a href="#solCertificacionesModal" data-toggle="modal"
                                               data-id="{{$pac->id}}"
                                               class="btn btn-xs btn-info tip" data-placement="top"
                                               title="Solicitar Cert. Presupuestaria y/o CPAC  ">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                        @endif
                                        {{--Si es un proceso pac y no es infima cuantia--}}
                                        @if($pac->proceso_pac==\App\Pac::PROCESO_PAC && $pac->procedimiento!='ÍNFIMA CUANTÍA')
                                            {{--si tiene el permiso cetificacion-pac--}}
                                            @permission('certificacion-pac')
                                            {{--Genarar CPAC si tiene rol financiero o es root y no exite una certificacion o no esta activa--}}
                                            {{--@if ( (Auth::user()->hasRole('financiero') || Auth::user()->hasRole('root')) && (is_null($pac->certificado_file) ||  $pac->certificado_status==\App\Cpac::CPAC_INACTIVA) )--}}
                                            {{--Generar siempre si el disponible es > a 0--}}
                                            @if ( (Auth::user()->hasRole('financiero') || Auth::user()->hasRole('root')) && $pac->disponible>0 )
                                                <a href="{{route('admin.pacs.certificacion-pac',$pac->id)}}"
                                                   class="btn btn-xs btn-danger tip" data-placement="top"
                                                   title="Generar Certificación PAC" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @endif
                                            {{-- Subir PDF CPAC --}}
                                            {{--Si el usuario tiene rol financiero o es root y exite una certificacion sin archivo subido y no esta activa--}}
                                            @if ( (Auth::user()->hasRole('financiero') || Auth::user()->hasRole('root')) && (is_null($pac->certificado_file) || $pac->certificado_status==\App\Cpac::CPAC_INACTIVA) )
                                                <a href="#subirCPAC" data-toggle="modal" data-id="{{$pac->id}}"
                                                   data-backdrop="static" data-keyboard="false"
                                                   class="btn btn-xs btn-success tip" data-placement="top"
                                                   title="Subir Certificación PAC">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            @endpermission
                                            {{-- Descargar PDF CPAC --}}
                                            {{--Si el usuario autenticado es el dueño del proceso y tiene el role (responsable-pac), o si es root o administrador o financiero, y ademas la cpac se subio y esta activa--}}
                                            @if (( ( Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac') ) || ( Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador') || Auth::user()->hasRole('financiero')) ) && (!is_null($pac->certificado_file) && $pac->certificado_status==\App\Cpac::CPAC_ACTIVA))
                                                <a href="{{route('pac.CPACdownload',$pac->id)}}"
                                                   class="btn btn-xs btn-primary tip" data-placement="top"
                                                   title="Descargar Certificación">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                        {{--FIN CERTIFICACION PAC (CPAC)--}}
                                    </td>
                                    <td>
                                        {{--CERTIFICACION PRESUPUESTARIA--}}
                                        @permission('cert-presup')
                                        <a href="#subirCPresup" data-toggle="modal" data-id="{{$pac->id}}"
                                           data-backdrop="static" data-keyboard="false"
                                           class="btn btn-xs btn-success tip" data-placement="top"
                                           title="Subir Certificación Presupuestaria">
                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                        </a>
                                        @endpermission
                                        {{--Si el usuario autenticado es el dueño del proceso y tiene el role (responsable-pac), o si es root o administrador o financiero, y ademas la cert presupuestaria se subio y esta activa--}}
                                        @if (( ( Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac') ) || ( Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador') || Auth::user()->hasRole('financiero')) ) && (!is_null($pac->cert_presup_file) && $pac->cert_presup_status==\App\Cpresupuestaria::CPRES_ACTIVA))
                                            <a href="{{route('pac.CPresupDownload',$pac->id)}}"
                                               class="btn btn-xs btn-primary tip" data-placement="top"
                                               title="Descargar Certificación Presupuestaria">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif
                                        {{--FIN CERTIFICACION PRESUPUESTARIA--}}
                                    </td>
                                    <td>
                                        {{--ACCIONES--}}

                                        {{--A LIBERAR para reforma--}}
                                        {{--Si es un proceso pac--}}
                                        @if ($pac->proceso_pac==\App\Pac::PROCESO_PAC)
                                            {{--Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac) o si es root o administrador y ademas la srpac se subio y esta activa y el disponible es mayor que 0--}}
                                            @if ( ((Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac')) || ( Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador'))) && (!is_null($pac->srpac_file) && $pac->srpac_status==\App\Srpac::SRPAC_ACTIVA) && $pac->disponible>0 )
                                                <a href="{{route('pac.getLiberar',$pac->id)}}"
                                                   class="btn btn-xs btn-warning tip" data-placement="top"
                                                   title="Liberar para Reforma">L
                                                </a>
                                            @endif
                                        @endif
                                        {{--no es proceso pac y hay disponibilidad, no necesita docum--}}
                                        @if ($pac->proceso_pac==\App\Pac::NO_PROCESO_PAC && $pac->disponible>0)
                                            <a href="{{route('pac.getLiberar',$pac->id)}}"
                                               class="btn btn-xs btn-warning tip" data-placement="top"
                                               title="Liberar para Reforma">L
                                            </a>
                                        @endif
                                        {{--FIN LIBERAR--}}

                                        {{--REFORMAR--}}
                                        @if ($pac->proceso_pac==\App\Pac::PROCESO_PAC && $pac->liberado > 0 )
                                            {{--Si hay monto liberado y se subio el pdf de la srpac o si no es un proceso pac--}}
                                            @if((!is_null($pac->srpac_file) && $pac->srpac_status==\App\Srpac::SRPAC_ACTIVA) || ($pac->proceso_pac== \App\Pac::NO_PROCESO_PAC))
                                                <a href="#tipoReformaModal" data-toggle="modal" data-id="{{$pac->id}}"
                                                   class="btn btn-xs btn-danger tip botonReform" data-placement="top"
                                                   title="Solicitud de Reforma"><i class="fa fa-recycle"
                                                                                   aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @elseif($pac->proceso_pac==\App\Pac::NO_PROCESO_PAC && $pac->liberado > 0)
                                            <a href="#tipoReformaModal" data-toggle="modal" data-id="{{$pac->id}}"
                                               class="btn btn-xs btn-danger tip botonReform" data-placement="top"
                                               title="Solicitud de Reforma"><i class="fa fa-recycle"
                                                                               aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        {{--FIN REFORMAR--}}

                                        {{--GESTION de procesos--}}
                                        @permission('gestion-procesos')
                                        {{--Si es un proceso pac y hay disponible--}}
                                        @if ($pac->proceso_pac== \App\Pac::PROCESO_PAC && $pac->disponible > 0)
                                            {{--Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac), o si es root , y ademas exite la certificacion cpac y cert presupuestaria y estan activas --}}
                                            @if (
                                            ( (Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac')) || (Auth::user()->hasRole('root')) ) && ( (!is_null($pac->certificado_file) && $pac->certificado_status==\App\Cpac::CPAC_ACTIVA) && (!is_null($pac->cert_presup_file) && $pac->cert_presup_status==\App\Cpresupuestaria::CPRES_ACTIVA)) )
                                                <a href="{{route('admin.gestion.create',$pac->id)}}"
                                                   class="btn btn-xs btn-primary tip"
                                                   data-placement="top" title="Gestión">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @elseif ($pac->proceso_pac== \App\Pac::NO_PROCESO_PAC && $pac->disponible>0)
                                            {{--Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac), o si es root , y ademas exite la cert presupuestaria y esta activa --}}
                                            @if (
                                            ( (Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac')) || (Auth::user()->hasRole('root')) ) && (!is_null($pac->cert_presup_file) && $pac->cert_presup_status==\App\Cpresupuestaria::CPRES_ACTIVA) )
                                                <a href="{{route('admin.gestion.create',$pac->id)}}"
                                                   class="btn btn-xs btn-primary tip"
                                                   data-placement="top" title="Gestión">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @endpermission
                                        {{--FIN GESTION--}}

                                        {{--VER GESTION Procesos--}}
                                        @if ($pac->comprometido>0 || $pac->devengado > 0)
                                            {{--Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac), o si es root o financiero --}}
                                            @if ( (Auth::user()->worker_id==$pac->trabajador_id && Auth::user()->hasRole('responsable-pac')) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('financiero')) )
                                                <a href="{{route('admin.gestion.show',$pac->id)}}"
                                                   class="btn btn-xs btn-info tip"
                                                   data-placement="top" title="Ver Gestión"><i class="fa fa-eye"
                                                                                               aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @endif
                                        {{--FIN VER GESTION--}}

                                        {{--FIN ACCIONES--}}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{--./panel-success--}}
    </div>{{--./col-md-12--}}

    @include('pac.tipo-reforma-modal')
    @include('inclusion.subir-incpac-modal')
    @include('srpac.subir-srpac-modal')
    @include('pac.subir-cpac-modal')
    @include('pac.sol_cpac_presup_modal')
    @include('pac.subir-cert-presup-modal')

@endsection


@section('scripts')
    <script src="{{asset('js/renderSection.js')}}"></script>
    <script src="{{asset('plugins/jquery-bootstrap-waitingFor/bootstrap-waitingfor.js')}}"></script>

    <script>

        $(document).ready(function () {

            $(".form_noEnter").keypress(function (e) {
                if (e.which === 13) {
                    return false;
                }
            });

            /************** INCLUSION PAC *******************/

            //modal para subir archivo inc pac
            $('#subirIncPAC').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var pac_id = button.data('id');
                $("#incpac-file-form").trigger('reset');
                var modal = $(this);
                modal.find('.modal-content #inclusion_pac_id').val(pac_id);
            });

            //enviar form del modal de inc pac
            $('form#incpac-file-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);
                var token = $("input[name=_token]").val();
                var formData = new FormData(document.getElementById("incpac-file-form"));//se envia tod el form al controlador, inc file
                var formAction = form.attr('action'); // form handler url, route
                var formMethod = form.attr('method'); // GET, POST
                waitingDialog.show('Cargando ...', {
                    headerText: 'Validando y enviando información...',
                    headerSize: 3,
                    dialogSize: 'm',
                    progressType: 'primary'
                });
                $.ajax({
                    url: formAction,
                    data: formData,
                    type: formMethod,
                    headers: {'X-CSRF-TOKEN': token},
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.message_error) {
                            swal('ERROR', response.message_error, 'error');
                            waitingDialog.hide();
                        } else {
                            waitingDialog.hide();
                            swal('', response.message, 'success');
                            $("#subirIncPAC").modal('hide');
                        }
                    },
                    error: function (response) {
                        waitingDialog.hide('', {
                            onHide: swal('', 'Complete los datos del formulario', 'error')
                        });
                        if (response.status === 422) {
                            var data = response.responseJSON.errors;
                            $.each(data, function (index, value) {
                                showValidationErrors(index, value[0])
                            });
                        }
                    }
                });
                return false; // evitar se envie el form
            });

            /************** FIN INCLUSION PAC *******************/

            /************** SRPAC *******************/

            //modal para subir archivo srpac
            $('#subirSRPAC').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var pac_id = button.data('id');
                $("#srpac-file-form").trigger('reset');
                var modal = $(this);
                modal.find('.modal-content #srpac_pac_id').val(pac_id);
            });

            //enviar form del modal de inc pac
            $('form#srpac-file-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);
                var token = $("input[name=_token]").val();
                var formData = new FormData(document.getElementById("srpac-file-form"));//se envia tod el form al controlador, inc file
                var formAction = form.attr('action'); // form handler url, route
                var formMethod = form.attr('method'); // GET, POST
                waitingDialog.show('Cargando ...', {
                    headerText: 'Validando y enviando información...',
                    headerSize: 3,
                    dialogSize: 'm',
                    progressType: 'primary'
                });
                $.ajax({
                    url: formAction,
                    data: formData,
                    type: formMethod,
                    headers: {'X-CSRF-TOKEN': token},
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.message_error) {
                            swal('ERROR', response.message_error, 'error');
                            waitingDialog.hide();
                        } else {
                            waitingDialog.hide();
                            swal('', response.message, 'success');
                            $("#subirSRPAC").modal('hide');
                        }
                    },
                    error: function (response) {
                        waitingDialog.hide('', {
                            onHide: swal('', 'Complete los datos del formulario', 'error')
                        });
                        if (response.status === 422) {
                            var data = response.responseJSON.errors;
                            $.each(data, function (index, value) {
                                showValidationErrors(index, value[0])
                            });
                        }
                    }
                });
                return false; // evitar se envie el form
            });

            /************** FIN SRPAC *******************/

            /************** CPAC *******************/

            //modal para subir archivo cpac
            $('#subirCPAC').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var pac_id = button.data('id');
                $("#cpac-file-form").trigger('reset');
                var modal = $(this);
                modal.find('.modal-content #cpac_pac_id').val(pac_id);
            });

            //enviar form del modal de inc pac
            $('form#cpac-file-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);
                var token = $("input[name=_token]").val();
                var formData = new FormData(document.getElementById("cpac-file-form"));//se envia tod el form al controlador, inc file
                var formAction = form.attr('action'); // form handler url, route
                var formMethod = form.attr('method'); // GET, POST
                waitingDialog.show('Cargando ...', {
                    headerText: 'Validando y enviando información...',
                    headerSize: 3,
                    dialogSize: 'm',
                    progressType: 'primary'
                });
                $.ajax({
                    url: formAction,
                    data: formData,
                    type: formMethod,
                    headers: {'X-CSRF-TOKEN': token},
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.message_error) {
                            swal('ERROR', response.message_error, 'error');
                            waitingDialog.hide();
                        } else {
                            waitingDialog.hide();
                            swal('', response.message, 'success');
                            $("#subirCPAC").modal('hide');
                        }
                    },
                    error: function (response) {
                        waitingDialog.hide('', {
                            onHide: swal('', 'Complete los datos del formulario', 'error')
                        });
                        if (response.status === 422) {
                            var data = response.responseJSON.errors;
                            $.each(data, function (index, value) {
                                showValidationErrors(index, value[0])
                            });
                        }
                    }
                });
                return false; // evitar se envie el form
            });

            /************** FIN CPAC *******************/


            /************** CERTIFICACION PRESUPUESTARIA *******************/

            //modal para subir archivo cert presup
            $('#subirCPresup').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var pac_id = button.data('id');
                $("#cpresup-file-form").trigger('reset');
                var modal = $(this);
                modal.find('.modal-content #cpresup_pac_id').val(pac_id);
            });

            //enviar form del modal
            $('form#cpresup-file-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);
                var token = $("input[name=_token]").val();
                var formData = new FormData(document.getElementById("cpresup-file-form"));//se envia tod el form al controlador, inc file
//                var formData = form.serialize(); // form data as string, no envia el file
                var formAction = form.attr('action'); // form handler url, route
                var formMethod = form.attr('method'); // GET, POST
                waitingDialog.show('Cargando ...', {
                    headerText: 'Validando y enviando información...',
                    headerSize: 3,
                    dialogSize: 'm',
                    progressType: 'primary'
                });
                $.ajax({
                    url: formAction,
                    data: formData,
                    type: formMethod,
                    headers: {'X-CSRF-TOKEN': token},
                    cache: false,
                    processData: false,
                    contentType: false,
//                    beforeSend : function() {
//                        console.log(formData);
//                    },
                    success: function (response) {
                        if (response.message_error) {
                            swal('ERROR', response.message_error, 'error');
                            waitingDialog.hide();
                        } else {
                            waitingDialog.hide();
                            swal('', response.message, 'success');
                            $("#subirCPresup").modal('hide');
                        }
                    },
                    error: function (response) {
                        waitingDialog.hide('', {
                            onHide: swal('', 'Complete los datos del formulario', 'error')
                        });
                        if (response.status === 422) {
                            var data = response.responseJSON.errors;
                            $.each(data, function (index, value) {
                                showValidationErrors(index, value[0])
                            });
                        }
                    }
                });
                return false; // evitar se envie el form
            });

            /************** FIN CERTIFICACION PRESUPUESTARIA *******************/

            //funcion para mostrar los errores de validacion ajax en el modal
            function showValidationErrors(name, error) {
                var group = $("#form-group-" + name);
                group.addClass('has-error');
                group.find('.help-block').text(error);
            }

            //funcion para limpiar los errores de validacion ajax en el modal
            function clearValidationError(name) {
                var group = $("#form-group-" + name);
                group.removeClass('has-error');
                group.find('.help-block').text('');
            }

            //limpia error en modal al teclear en el input
            $("#cod_cpresup").on('keyup', function () {
                clearValidationError($(this).attr('id').replace('#', ''))
            });
            //limpia error en modal al dar cambiar elemento, button, select, etc
            $("#cpresup-file, #incpac-file, #srpac-file, #cpac-file").on('change', function () {
                clearValidationError($(this).attr('id').replace('#', ''))
            });


            //comentado
            $("#imprimir_pdf").on('click', function (event) {
                event.preventDefault();
                var token = $("input[name=_token]").val();
                var form = $("#form_search");
                var url = "{{route('admin.pacs.pac-pdf')}}";
                var data = form.serialize();
                $.ajax({
                    url: url,
                    data: data,
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    success: function (response) {
                    },
                    error: function (response) {
                        console.log(response);

                    }
                });

            });


            var table = $("#pac_table").DataTable({
                lengthMenu: [[5, 10, -1], [5, 10, 'Todo']],
                "language": {
                    "decimal": "",
                    "emptyTable": "No se encontraron datos en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrados de un total _MAX_ registros)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encrontraron coincidencias",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Activar para ordenar ascendentemente",
                        "sortDescending": ": Activar para ordenar descendentemente"
                    },
                    "buttons": {
                        "colvis": "Columnas",
                        "copy": "Copiar",
                        "print": "Imprimir"
                    }
                },
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // formatear los datos para sumar
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    // Total en todas las paginas
                    total_presupuesto = api.column(7).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_ejecutado = api.column(8).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_devengado = api.column(9).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_disponible = api.column(10).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    total_reforma = api.column(11).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // Total en la pagina actual
                    pageTotal_pre = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_eje = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_dev = api.column(9, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_disp = api.column(10, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);
                    pageTotal_reform = api.column(11, {page: 'current'}).data().reduce(function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                    // actualzar total en el pie de tabla
                    $(api.column(7).footer()).html('$' + pageTotal_pre + '<p style="color: #0c199c">' + ' ( $' + total_presupuesto + ' )' + '</p>');
                    $(api.column(8).footer()).html('$' + pageTotal_eje + '<p style="color: #0c199c">' + ' ( $' + total_ejecutado + ' )' + '</p>');
                    $(api.column(9).footer()).html('$' + pageTotal_dev + '<p style="color: #0c199c">' + ' ( $' + total_devengado + ' )' + '</p>');
                    $(api.column(10).footer()).html('$' + pageTotal_disp + '<p style="color: #0c199c">' + ' ( $' + total_disponible + ' )' + '</p>');
                    $(api.column(11).footer()).html('$' + pageTotal_reform + '<p style="color: #0c199c">' + ' ( $' + total_reforma + ' )' + '</p>');
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'PAC - AREA',
                        message: 'Presupuesto anual de compras ',
                        orientation: 'landscape',
                        pageSize: 'letter',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ],
                columnDefs: [{
//                    targets: -1,
                    visible: false
                }]
            });
//
//            table.buttons().container()
//                    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
            table.buttons().container()
                .appendTo('#botones_imprimir');

            $("#pac_table").fadeIn();


            $('#pac_table .search-filter').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" style="width: 100%" placeholder="' + title + '" />');
            });

            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });

        });

        $(document).on('mouseover', '.tip', function (event) {
            $(this).tooltip();
        });

        //enviar info al modal tipo de reforma antes de cargarlo
        $('#tipoReformaModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // boton del que lanza el modal
            var pac_id = button.data('id'); // extraer info id del pac data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);
            modal.find('.modal-content #to_reform_pac_id').val(pac_id);
        });

        //enviar el form tipo de reforma al dar en enviar en el modal
        $(document).on('click', '#send_reform', function (e) {
            e.preventDefault();
            var form = $("#reform-form");
            form.submit();
        });

        //enviar info al modal de sol CPAC y presupuestaria antes de cargarlo
        $('#solCertificacionesModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // boton del que lanza el modal
            var pac_id = button.data('id'); // extraer info id del pac data-* attributes
            var url = "{{route('admin.pac.sol_certificacion',':ID')}}";
            var route = url.replace(':ID', pac_id);
            var token = $("input[name=_token]").val();
            var notas_sol = $("#notas_sol");
            notas_sol.val('');
            // $("#form-update").trigger('reset');
            $.ajax({
                url: route,
                type: "GET",
                headers: {'X-CSRF-TOKEN': token},
                success: function (response) {
                    var monto = Math.round((parseFloat(response.pac.presupuesto) / 1.12) * 100) / 100;
                    $("#concepto_sol").val(response.pac.concepto);
                    $("#monto_sol").val(monto);
                    $("#cod_item_sol").val(response.codigos.cod_item);
                    $("#actividad_sol").val(response.codigos.cod_actividad);
                    $("#prog_sol").val(response.codigos.cod_programa);
                    $("#item_sol").val(response.codigos.item);

                    if ((response.pac.proceso_pac === '0') || (response.pac.proceso_pac === '1' && response.pac.procedimiento === 'ÍNFIMA CUANTÍA')) {
                        $("#user_to_cpac").hide();
                    } else {
                        $("#user_to_cpac").show();
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
            var modal = $(this);
            modal.find('.modal-content #pac_id_sol').val(pac_id);
        });

        //enviar el form al dar click en el modal de sol de certificaiones presupuestaria y cpac por correo
        $(document).on('click', '#send_solicitud_certificacion', function (e) {
            e.preventDefault();
            var form = $("#sol-cert-form");
            form.submit();
        });

        paceOptions = {
            // Disable the 'elements' source
            elements: false,

            // Only show the progress on regular and ajax-y page navigation,
            // not every request
            restartOnRequestAfter: true
        }

    </script>

@endsection