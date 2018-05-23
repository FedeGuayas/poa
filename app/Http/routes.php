<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', ['uses'=>'HomeController@index', 'as'=>'inicio']);

// Authentication Routes...
$this->get('login', 'Auth\AuthController@showLoginForm');
$this->post('login', ['uses'=>'Auth\AuthController@login', 'as'=>'login']);
$this->get('logout', ['uses'=>'Auth\AuthController@logout', 'as'=>'logout']);

// Registration Routes...
//$this->get('register', 'Auth\AuthController@showRegistrationForm');
//$this->post('register', 'Auth\AuthController@register');

// Password Reset Routes...
$this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
$this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
$this->post('password/reset', 'Auth\PasswordController@reset');

Route::group(['prefix' => 'admin','middleware'=>'auth'], function () {

    Route::post('importPOA',['uses'=>'AperturaController@importPOA', 'as'=>'importPOA']);
    Route::post('resetPOA',['uses'=>'AperturaController@resetPOA', 'as'=>'resetPOA']);
    Route::get('loadPOA',['uses'=>'AperturaController@loadPOA', 'as'=>'loadPOA']);
    Route::get('poaesigef', ['uses'=>'AperturaController@listPOA', 'as'=>'poa']);
    Route::get('importAct',['uses'=>'ActividadController@importAct', 'as'=>'importAct']);
    Route::get('config',['uses'=>'AperturaController@getConfig', 'as'=>'configurationGet']);
    Route::post('config',['uses'=>'AperturaController@postConfig', 'as'=>'configurationPost']);
    
    
    Route::get('programa/{id}/actividades',['uses'=>'ProgramaController@loadActividades', 'as'=>'loadActividades']);
    Route::post('programa/{id}/actividades{actividades?}',['uses'=>'ProgramaController@asociarActividades', 'as'=>'asociarActividades']);

    Route::get('presupuesto/load',['uses'=>'ItemController@loadPresupuesto', 'as'=>'loadPresupuesto']);
    Route::post('presupuesto/import',['uses'=>'ItemController@importPresupuesto', 'as'=>'importPresupuesto']);
    
    //Route::post('importPAC',['uses'=>'ItemController@importPAC', 'as'=>'importPAC']);

    Route::get('item/ingresos{data?}',['uses'=>'ExtraController@loadExtra', 'as'=>'loadExtra']);

    Route::get('workers/departamento{data?}',['uses'=>'WorkerController@getDpto', 'as'=>'getDpto']);
   
//    Route::get('pacs/lists/areas',['uses'=>'PacController@indexPacArea', 'as'=>'indexPacArea']);
    Route::get('pac/create/{area_item}',['uses'=>'PacController@createPac', 'as'=>'createPac']);
    Route::get('pacs/areas',['uses'=>'PacController@indexPlanificacion', 'as'=>'indexPlanificacion']);
    Route::get('pacs/gestion{id}',['uses'=>'PacController@confirmarDevengado', 'as'=>'confirmarDevengado']);
    Route::get('pacs/pdf/{data?}',['uses'=>'PacController@pacsPDF', 'as'=>'admin.pacs.pac-pdf']);

    //Vista planificacion para agregar inclusion Pac o No Pac
    Route::get('pacs/inclusion/areas',['uses'=>'InclusionController@indexIncPac', 'as'=>'indexIncPac']);
    //vista para generar el pdf de la Solicitud de Inclusion PAC
    Route::get('pacs/inclusion_pac/create',['uses'=>'InclusionController@createIPAC', 'as'=>'create.inclusion-pac']);
    //guardar info de la Solicitud de Inclusion PAC
    Route::post('pacs/inclusion_pac/store',['uses'=>'InclusionController@storeIPAC', 'as'=>'store.inclusion-pac']);
    //subir el pdf de la Solicitud de Inclusión PAC
    Route::post('pacs/inclusion/inc-pac-file',['uses'=>'InclusionController@postFileIncPAC','as'=>'pac.inclusion.postFileIncPAC']);
    //Descargar pdf de la Inclsusion PAC
    Route::get('pacs/inclusion/{pac_id}/IncPACDownload',['uses'=>'InclusionController@IncPacDownload','as' => 'pac.inclusion.IncPacDownload']);

    //admin.pac.sol_certificacion
    Route::get('pac/{pac_id}/sol_cert_pac_presp',['uses'=>'PacController@sol_cert','as' => 'admin.pac.sol_certificacion']);

    Route::get('pac/sol-certificaciones/{data?}',['uses'=>'PacController@sendSolCertificaciones', 'as'=>'solCertPacPresup']);

    //subir el pdf de la Solicitud de reforma PAC (SRPAC)
    Route::post('pac/srpac-file',['uses'=>'SrpacController@postFileSRPAC','as'=>'pac.postFileSRPAC']);
    //Descargar pdf de la Solicitud de reforma PAC (SRPAC)
    Route::get('pac/{pac_id}/SRPACDownload',['uses'=>'SrpacController@SRPACDownload','as' => 'pac.SRPACdownload']);

    //generar el pdf de la CPAC
    Route::get('pacs/certificacion-pac/{pac}',['uses'=>'CpacController@certificacionPDF', 'as'=>'admin.pacs.certificacion-pac']);
    //subir el pdf CPAC
    Route::post('pac/cpac-file/upload',['uses'=>'CpacController@postFileCPAC','as'=>'pac.postFileCPAC']);
    //Descargar pdf CPAC
    Route::get('pac/{pac_id}/CPACDownload',['uses'=>'CpacController@CPACDownload','as' => 'pac.CPACdownload']);

    //subir el pdf de Certificacion Presupuestaria
    Route::post('pac/cert-presup-file/upload',['uses'=>'PacController@postFileCPresup','as'=>'pac.postFileCPresup']);
    //Descargar pdf CPAC
    Route::get('pac/{pac_id}/CPresupDownload',['uses'=>'PacController@CPresupDownload','as' => 'pac.CPresupDownload']);

    //Cargar vista para liberar recursos a reformar
    Route::get('pac/{pac_id}/liberar',['uses'=>'PacController@getLiberar','as'=>'pac.getLiberar']);
    //permitir reformar disponible del proceso pac
    Route::put('pac/{pac_id}/to_reform',['uses'=>'PacController@permitReform','as'=>'pac.permitReform']);

//borrar es para probar pdf del informe tecnico de la reforma
    Route::get('reform/{reforma}/informe',['uses'=>'ReformaController@verInformePDF', 'as'=>'admin.reforma.verinforme']);

    //area_item (poafdg) planificacion
    Route::get('poafdg/planificacion',['uses'=>'PoaController@poaFDG', 'as'=>'poaFDG']);
    Route::get('item',['uses'=>'PoaController@getItem', 'as'=>'getItem']);
    Route::get('codigo',['uses'=>'PoaController@getUniqueItem', 'as'=>'getUniqueItem']);
    Route::get('poafdg/area/{data?}',['uses'=>'PoaController@loadItemArea', 'as'=>'loadItemArea']);
    Route::post('poafdg/planificacion',['uses'=>'PoaController@storePlanificacion', 'as'=>'storePlanificacion']);

    //vista agregar inclusion
    Route::get('poafdg/inclusion',['uses'=>'InclusionController@poaInclusion', 'as'=>'admin.inclusion']);
    Route::get('poafdg/inclusion/getitem',['uses'=>'InclusionController@getItem', 'as'=>'inclusion.getItem']);
    Route::get('poafdg/inclusion/codigo',['uses'=>'InclusionController@getUniqueItem', 'as'=>'inclusion.getUniqueItem']);
    Route::get('poafdg/inclusion/area/{data?}',['uses'=>'InclusionController@loadItemArea', 'as'=>'inclusion.loadItemArea']);
    Route::post('poafdg/inclusion',['uses'=>'InclusionController@storeInclusion', 'as'=>'storeInclusion']);
    Route::delete('poafdg/inclusion/{id}/eliminar',['uses'=>'InclusionController@destroy', 'as'=>'destroyInclusion']);
    //vista para crear el proceso de la inclusion a nivel de area
    Route::get('pac/create/inclusion/{area_item}',['uses'=>'InclusionController@createPacInclusion', 'as'=>'createPacInclusion']);
    Route::post('pac/store/inclusion',['uses'=>'InclusionController@storePacInclusion', 'as'=>'storePacInclusion']);

    Route::get('reformas/solicitud',['uses'=>'ReformaController@createReforma', 'as'=>'createReforma']);//origen reforma
    Route::get('reformas/destino',['uses'=>'ReformaController@destino', 'as'=>'destinoReforma']);//destino reforma
    Route::post('reformas/pacs_destino',['uses'=>'ReformaController@storePacsDestino', 'as'=>'admin.reformas.store.destino']);
    Route::get('reformas/confirm/{reforma}',['uses'=>'ReformaController@confirm', 'as'=>'admin.reformas.confirm']);

    Route::get('reportes/control_seguimiento',['uses'=>'ReportController@control_seguimiento', 'as'=>'admin.reportes.control_seguimiento']);
    Route::get('reportes/resumen_mensual',['uses'=>'ReportController@resumenMensual', 'as'=>'admin.reportes.resumen_mensual']);
    Route::get('reportes/reforma/{id}',['uses'=>'ReportController@reformaPDF', 'as'=>'admin.reportes.reforma-pdf']);
    Route::post('reportes/reformas/export/',['uses'=>'ReportController@reformaSelectPDF', 'as'=>'admin.reportes.reformas_select-pdf']);

    Route::get('historico/cierre',['uses'=>'HistoricoController@cierre', 'as'=>'admin.historico.cierre']);
    Route::get('historico/export',['uses'=>'HistoricoController@exportHistorico', 'as'=>'admin.historico.export']);

    //vista para editar la contraseña del perfil de usuario
    Route::get('/password/edit', ['uses' => 'UserController@getPasswordEdit','as' => 'user.password.edit']);
    //actualizar la contraseña del perfil de usuario
    Route::put('user/{user}/update', ['uses' => 'UserController@postPassword','as' => 'user.password.update']);
    //adicionar roles a los usuarios
    Route::get('user/{id}/roles', ['as' => 'admin.users.roles','uses'=>'UserController@roles' ]);
    Route::put('user/{id}/setroles', ['as' => 'admin.users.setroles','uses'=>'UserController@setRoles' ]);

    //asignar permisos a los roles
    Route::get('rol/{id}/perms', ['as' => 'admin.roles.perms','uses'=>'RolesController@permisos' ]);
    Route::put('rol/{id}/set-permisos', ['as' => 'admin.roles.setpermisos','uses'=>'RolesController@setPermisos' ]);


    Route::resource('apertura', 'AperturaController');
    Route::resource('areas', 'AreaController');
    Route::resource('departamentos', 'DepartamentoController');
    Route::resource('programas', 'ProgramaController');
    Route::resource('actividades', 'ActividadController');
    Route::resource('items', 'ItemController');
    Route::resource('poa', 'PoaController');
    Route::resource('ingresos', 'ExtraController');
    Route::resource('pacs', 'PacController');
    Route::resource('workers', 'WorkerController');
    Route::resource('gestion', 'DetalleController');
    Route::resource('reformas', 'ReformaController');
    Route::resource('historico', 'HistoricoController');
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RolesController');
    Route::resource('permissions', 'PermissionsController');
    Route::resource('srpacs', 'SrpacController',['except'=>['index','show','edit','update','destroy']]);
//    Route::get('config/cierre',['uses'=>'AperturaController@cierreMensual', 'as'=>'cierreMensual']);

});
