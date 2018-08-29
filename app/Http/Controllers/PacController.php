<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Cpac;
use App\Cpresupuestaria;
use App\Detalle;
use App\InclusionPac;
use App\Pac;
use App\PacDestino;
use App\PacOrigen;
use App\ReformType;
use App\Srpac;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;

use App\Http\Requests\PacStoreRequest;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class PacController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware(['role:root|administrador'], ['except' => ['index','indexPlanificacion']]);
    }

    /**
     * Vista para crear la planificacion de las areas, creando los procesos que no sea inclusion PROCESOS/Planifiacacion
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPlanificacion(Request $request)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $areas = Area::all();
            $list_areas = $areas->pluck('area', 'id');

            $area_select = $request->input('area');

            $area = Area::where('id', $area_select)->first();

            $area_item = DB::table('area_item as ai')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('months as m', 'm.cod', '=', 'ai.mes')
                ->leftJoin('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->select('ai.id', 'i.cod_item', 'ai.inclusion', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'm.month as mes', 'a.area', DB::raw('sum(p.presupuesto) as distribuido'))
                ->groupBy('ai.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'mes', 'a.area')
                ->where('area_id', 'like', '%' . $area_select . '%')
                ->where('ai.inclusion', '=', AreaItem::INCLUSION_NO)//mostrar los poas que no sean una inclusion
                ->get();

            $pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.comprometido', 'p.devengado', 'p.inclusion', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'i.cod_programa', 'i.cod_actividad')
                ->where('a.id', $area_select)
                ->where('p.inclusion', Pac::PROCESO_INCLUSION_NO)//mostrar los pac que no sean inclusion
                ->get();


            $pacs = collect($pacs);

            //Filtro para enviar a la vista solo los pacs necesarios
            $pacs = $pacs->filter(function ($pac, $key) use ($user_login) {
                //Si el usuario pertenece al area a la que se repartio el dinero,
                //Si no se han realizado movimientos de dinero en el pac
                if (($user_login->worker->departamento->area->area == $pac->area || $user_login->hasRole('root')) && $pac->presupuesto == $pac->disponible) {
                    return true;
                } else return false;
            })->values();

            return view('pac.planificacion', compact('list_areas', 'area_item', 'area_select', 'pacs', 'area'));
        } else return abort(403);
    }

    /**
     * Lista todos los procesos PROCESOS/Procesos
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_login = $request->user();

        $fecha_actual = Carbon::now();
        $mes_actual = $fecha_actual->month;

        $reformas = ReformType::all();
        $list_reformas = $reformas->pluck('tipo_reforma', 'id');

        $areas = Area::all();
        $list_areas = $areas->pluck('area', 'id');


        $area_select = $request->input('area');

        if (is_null($area_select)) {
            $area_select = $user_login->worker->departamento->area_id;
        }

        $area = Area::where('id', $area_select)->first();

        $pacs = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->join('departamentos as d', 'd.id', '=', 'w.departamento_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'm.cod', 'p.presupuesto', 'p.disponible', 'p.devengado', 'p.liberado', 'p.reform', 'w.nombres', 'w.apellidos', 'w.id as trabajador_id', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'p.cpc', 'i.cod_programa', 'i.cod_actividad', 'd.area_id as area_trabajador', 'd.departamento', 'ai.area_id as aiID', 'p.proceso_pac', 'p.inclusion')
            ->when($area_select != '', function ($query) use ($area_select) {
                return $query->where('ai.area_id', '=', $area_select);
            }, function ($query) use ($area_select) {
                return $query->where('ai.area_id', 'like', '%' . $area_select . '%');
            })
            ->get();

        $pacs = collect($pacs);//convierto a colleccion para poder utilizar map()

        //Filtro para enviar a la vista solo lo necesario
        $pacs = $pacs->filter(function ($item, $key) use ($user_login) {
            //   Si el usuario autenticado es el dueño del proceso y si tiene el role (responsable-pac),
            //   o el trabajador pertenece al area que se asigno el pac y es analista o responsable-poa,
            //   o  es root, administrador o financiero, mostrarlo
            if ((($user_login->worker_id == $item->trabajador_id) && $user_login->hasRole('responsable-pac') || ($user_login->worker->departamento->area_id == $item->area_trabajador && ($user_login->hasRole('analista') || $user_login->hasRole('responsable-poa')))) || ($user_login->hasRole('root') || $user_login->hasRole('administrador') || $user_login->hasRole('financiero'))) {
                return true;
            } else return false;
        })->values();

//            recorro cada elemento de la coleccion para agregar un nuevo elemento donde indico si tiene un CPAC, cert presup, srpac, inclusion
        $pacs->map(function ($pac) use ($user_login) {

            $cpac = Cpac::where('pac_id', $pac->id)->select('certificado', 'status')->get()->last(); //ultimo pdf de CPAC subido
            $cpresup = Cpresupuestaria::where('pac_id', $pac->id)->select('cert_presup', 'status')->get()->last(); //ultimo pdf de Cert Presup. subido
            $srpac = Srpac::where('pac_id', $pac->id)->select('solicitud_file', 'status')->get()->last(); //ultimo pdf de Srpac subido
            $inclupac = InclusionPac::where('pac_id', $pac->id)->select('inclusion_file', 'status')->get()->last(); //ultimo pdf de inclusion pac subido
            $cert_pac = null;
            $cert_pac_status = null;
            $cert_presup_file = null;
            $cert_presup_status = null;
            $sol_rpac = null;
            $sol_rpac_status = null;
            $incl_pac_file = null;
            $incl_pac_status = null;
            if ($cpac) { //si tiene un pdf asignado a la cpac lo agrego
                $cert_pac = $cpac->certificado;
                $cert_pac_status = $cpac->status;
            }
            if ($srpac) { //si tiene un pdf asignado a la srpac lo agrego
                $sol_rpac = $srpac->solicitud_file;
                $sol_rpac_status = $srpac->status;
            }
            if ($inclupac) { //si tiene un pdf asignado a la inclusion pac lo agrego
                $incl_pac_file = $inclupac->inclusion_file;
                $incl_pac_status = $inclupac->status;
            }
            if ($cpresup) { //si tiene un pdf asignado a la cert presupuestaria lo agrego
                $cert_presup_file = $cpresup->cert_presup;
                $cert_presup_status = $cpresup->status;
            }
            $pac->certificado_file = $cert_pac; //archivo cpac
            $pac->certificado_status = $cert_pac_status; //status archivo cpac
            $pac->cert_presup_file = $cert_presup_file; //archivo  cert presupuestaria
            $pac->cert_presup_status = $cert_presup_status; //status archivo  cert presupuestaria
            $pac->srpac_file = $sol_rpac; //archivo srpac
            $pac->srpac_status = $sol_rpac_status; //status archivo srpac
            $pac->inclusion_file = $incl_pac_file; //archivo  inclusion pac
            $pac->inclusion_file_status = $incl_pac_status; //status archivo  inclusion pac

            return $pac;
        });

        return view('pac.index', compact('list_areas', 'list_reformas', 'pacs', 'area_select', 'area', 'mes_actual'));
    }

    /**
     * Vista para crear Los Procesos para cada item por area  PROCESOS/Planificacion -> boton billete
     *
     * @return \Illuminate\Http\Response
     */
    public function createPac(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $area_item = AreaItem::
            join('months as m', 'm.cod', '=', 'area_item.mes')
                ->select('area_item.*', 'm.cod', 'm.month')
                ->where('area_item.id', $id)->first();

            $codigos = DB::table('area_item as ai')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item')
                ->where('ai.id', $id)
                ->first();

            $workers = Worker::select(DB::raw('concat (nombres," ",apellidos) as nombre,id'))->get();
            $list_workers = $workers->pluck('nombre', 'id');

            $area = Area::
            join('area_item as ai', 'ai.area_id', '=', 'a.id', 'as a')
                ->select('a.area')
                ->where('ai.id', $id)
                ->first();

            //valor asignado a este item en la planificacion del pac
            $pac_presupuesto = $area_item->pacs()->where('mes', $area_item->mes)->sum('presupuesto');

            //maximo que se puede distribuir al responsable de este pac
            $total_disponible = $area_item->monto - $pac_presupuesto;

            //procesos del poa y que no sean inclusion
            $pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'p.procedimiento', 'p.concepto')
                ->where('p.area_item_id', $id)
                ->where('p.inclusion', Pac::PROCESO_INCLUSION_NO)
                ->get();

            return view('pac.createPac', compact('area_item', 'list_workers', 'area', 'total_disponible', 'codigos', 'pacs'));
        } else return abort(403);
    }


    /**
     * Guardar el proceso
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PacStoreRequest $request)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $area_item = AreaItem::where('id', $request->area_item_id)->first();
            $worker = Worker::where('id', $request->worker)->first();
            $valor = $request->input('valor');
            $total_disponible = $request->input('total_disponible');
            $proceso_pac = $request->input('proceso_pac');

            if ($valor > $total_disponible) {
                return back()->withInput()->with('message_danger', 'No puede asignar un valor mayor a lo disponible');
            } else $presupuesto = $valor;

            $pac = new Pac();
            $pac->cod_item = $request->input('cod_item');
            $pac->item = $request->input('item');
            $pac->mes = $request->input('mes');
            $pac->concepto = strtoupper($request->input('concepto'));
            $pac->procedimiento = strtoupper($request->input('procedimiento'));
            $pac->tipo_compra = strtoupper($request->input('tipo_compra'));
            $pac->cpc = strtoupper($request->input('cpc'));
            $pac->disponible = $valor;
            if ($proceso_pac == 'on') {
                $pac->esProcesoPac();
            } else $pac->noEsProcesoPac();
            $pac->presupuesto = $presupuesto;
            $pac->area_item()->associate($area_item);
            $pac->worker()->associate($worker);
            $pac->save();

            return redirect()->route('indexPlanificacion')->with('message', 'Proceso guardado correctamente');
        } else return abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $pac = Pac::
            join('months as m', 'm.cod', '=', 'pacs.mes')
                ->select('pacs.*', 'm.month')
                ->where('pacs.id', $id)->first();

            $area_id = $request->input('area');

            $area_item = AreaItem::findOrFail($pac->area_item_id);

            //valor asignado a este item en la planificacion del pac
            $pac_presupuesto = $area_item->pacs()->where('mes', $area_item->mes)->sum('presupuesto');
            //$pacs2=AreaItem::has('pacs')->get();

            //maximo que se puede distribuir al responsable de este pac
            $total_disponible = $area_item->monto - $pac_presupuesto;

            $workers = Worker::select(DB::raw('concat (nombres," ",apellidos) as nombre,id'))->get();
            $list_workers = $workers->pluck('nombre', 'id');

            return view('pac.edit', compact('pac', 'total_disponible', 'list_workers'));

        } else return abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $pac = Pac::findOrFail($id);
            $worker = Worker::findOrFail($request->input('worker_id'));
            $presupuesto_actual = $pac->presupuesto;

            $proceso_pac = $request->input('proceso_pac');

            $presupuesto_nuevo = $request->input('presupuesto');
            $total_disponible = $request->input('total_disponible');

            if ($pac->tipo_compra != strtoupper($request->input('tipo_compra'))){
                return back()->with('message_danger', 'El tipo de compra solo puede ser cambiado mediante reforma');
            }

            if ($presupuesto_nuevo <= $presupuesto_actual) {
                $sub = $presupuesto_actual - $presupuesto_nuevo;
                $pac->presupuesto = $request->input('presupuesto');
                $pac->disponible = $pac->disponible - $sub;
            } else if ($presupuesto_nuevo > $presupuesto_actual && $total_disponible > 0 && ($presupuesto_nuevo - $presupuesto_actual) <= $total_disponible) {
                $sub = $presupuesto_nuevo - $presupuesto_actual;
                $pac->presupuesto = $request->input('presupuesto');
                $pac->disponible = $pac->disponible + $sub;
            } else if ($presupuesto_nuevo > $presupuesto_actual && $total_disponible <= 0) {
                return back()->with('message_danger', 'No puede asignar un valor mayor a lo disponible');
            } else //            if ($presupuesto_nuevo>$presupuesto_actual && $total_disponible > 0 && ($presupuesto_nuevo - $presupuesto_actual)>$total_disponible)
            {
                return back()->with('message_danger', 'La diferencia es mayor que el presupuesto disponible');
            }

            $pac->concepto = strtoupper($request->input('concepto'));
            $pac->procedimiento = strtoupper($request->input('procedimiento'));
            $pac->tipo_compra = strtoupper($request->input('tipo_compra'));
            $pac->cpc = strtoupper($request->input('cpc'));
            if ($proceso_pac == 'on') {
                $pac->esProcesoPac();
            } else $pac->noEsProcesoPac();
            $pac->worker()->associate($worker);
            $pac->update();
            return redirect()->route('indexPlanificacion')->with('message', 'PAC actualizado correctamente');

        } else return abort(403);
    }

    /**
     * Eliminar el proceso
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            try {
                DB::beginTransaction();

                $pac = Pac::findOrFail($id);

                $reforma_origen=PacOrigen::where('pac_id',$pac->id)->first();
                $reforma_destino=PacDestino::where('pac_id',$pac->id)->first();
                $gestion=Detalle::where('pac_id',$pac->id)->first();

                //si el proceso es una inclusion o  esta en alguna reforma o alguna gestion, no eliminar
                if ($pac->inclusion===Pac::PROCESO_INCLUSION_SI || $reforma_origen || $reforma_destino || $gestion){
                    $message = 'El proceso no puede ser eliminado porque o es una inclusión, o se encuentra asociado a una reforma o gestion';
                    return response()->json(['message' => $message,'tipo'=>'error']);
                }

                $pac->delete();
                $message = 'Proceso eliminado';

                DB::Commit();

                if ($request->ajax()) {
                    return response()->json(['message' => $message]);
                }

            } catch
            (\Exception $e) {
                DB::Rollback();
                $message = 'Existen gestiones vinculadass a esta proceso y no pueden ser eliminado';
//            $message = $e->getMessage();
                return response()->json(['message' => $message, 'tipo' => 'error']);
            }

        } else return abort(403);
    }

    /**
     * Confirmar proceso devengado
     */
    public function confirmarDevengado(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('aprueba-devengado')) {

            $gestion = Detalle::findOrFail($id);
            $importe = $gestion->importe;

            $pac = Pac::where('id', $gestion->pac_id)->first();
            $comprometido = $pac->comprometido - $importe;
            $devengado = $pac->devengado + $importe;

            if ($comprometido < 0) {
                $message = 'Ah ocurrido un error, el importe no debe ser mayor al ejecutado';
                return response()->json(['message' => $message, 'tipo' => 'error']);
            } else {
                $pac->comprometido = $comprometido;
                $pac->devengado = $devengado;
                $pac->update();
                $gestion->estado = 'Devengado';
                $gestion->update();
            }

            $message = 'Proceso devengado';
            if ($request->ajax()) {
                return response()->json(['message' => $message]);
            }

        } else return abort(403);
    }


    public function pacsPDF(Request $request)
    {
        $area_select = $request->input('area');
        $area = Area::where('id', $area_select)->first();
        $pacs = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'i.cod_programa', 'i.cod_actividad')
            ->where('a.id', $area_select)
            ->get();

//            PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')

        if ($request->ajax()) {
            $pdf = PDF::loadView('pac.pacs-pdf', compact('pacs', 'area'));
//            return $pdf->download('ComprobantePago.pdf');//descarga el pdf
            return $pdf->setPaper('letter', 'landscape')->stream('PAC-AREA');//imprime en pantalla
        }

    }


    /**
     * Cargar vista para liberar recursos
     * @param Request $request
     * @param $pac_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|void
     */
    public function getLiberar(Request $request, $pac_id)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

//            $data = $request->all();
//            $pac_id = key($data);
            $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

            $area_id = $user_login->worker->departamento->area_id;
            $area = Area::where('id', $area_id)->first();//area a la que pertenece el trabajador que inicio sesion

            if (count($area) > 0) {
                //trabajadores que pertenecen al mismo area del trabajador logeado
                $workers_all = Worker::with('user')->whereHas('departamento', function ($query) use ($area_id) {
                    $query->where('area_id', $area_id);
                })->get();

                //trabajadores del mismo area del usuario logeado y que tienen como rol analista(usuario con permisos para reformas)
                $workers = $workers_all->filter(function ($value, $key) {
                    return $value->user->hasRole('analista');
                })->values();

                //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
                $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                    $query->where('area_id', $area_id)
                        ->where('departamento', 'like', "%direcc%");
                })->first();

                $correo_para = [];
                foreach ($workers as $w) {
                    $correo_para[] = $w->email;
                }

                if (count($jefe_area) > 0) {
                    $correo_para[] = $jefe_area->email;
                }
                $correo_para = array_unique($correo_para);
            } else {
                return back()->withInput()->with('message_danger', 'Existe un error en el area asignada al usuario que inicio sección. Contacte al administrador del sistema');
            }
            return view('pac.liberar-reform', compact('pac', 'correo_para'));

        } else return abort(403);
    }


    /**
     * Liberar recursos del proceso para que pueda ser reformado
     * Permitir reformas sobre monto que no será utilizado
     */
    public function permitReform(Request $request, $pac_id)
    {
        $de = trim($request->input('user-from')); //"admin admin ( admin@mail.com )"
        $liberar = $request->input('reform_value');

        $parte1 = explode('(', $de); //array:2 [0 => "admin admin " 1 => " admin@mail.com )"]
        $parte2 = explode(')', $parte1[1]); //array:2 [0 => " admin@mail.com " 1 => ""]
        $user_from = trim($parte2[0]); //email del usuario que envia

        $para = trim($request->input('user-to'));
        $users_to = array_filter(explode(";", $para));//arreglo de direcciones de correo de los destinatarios

        $message_note = strtoupper(trim($request->input('message-note') . '. POR UN MONTO DE $' . $liberar));
        $message_text = strtoupper(trim($request->input('message-text')));

        $pac = Pac::where('id', $pac_id)->first();

        $disponible = $pac->disponible; //monto disponible en el pac

        if ($liberar <= $disponible) {
            $importe = $liberar;
            $disp = $pac->disponible - $importe;

            $pac->liberado = $pac->liberado + $importe;
            $pac->disponible = $disp;
        } else return back()->withInput()->with('message_danger', 'El importe a liberar no puede ser superior al disponible');

        $this->sendPacResourcesToReformUsersMail($user_from, $users_to, $message_note, $message_text);

        $pac->update();

        $message = 'Se liberaron recursos para reformas';
        return redirect()->route('admin.pacs.index')->with($message);
    }


    /**
     * Correo de notificacion de liberacion de recursos en un pac para los analistas de reformas
     * @param $user
     * @param $pass
     */
    public function sendPacResourcesToReformUsersMail($user_from, $users_to, $notes, $message_text)
    {
        Mail::send('emails.new_resources_to_reform', ['note' => $notes, 'message_text' => $message_text], function ($message) use ($user_from, $users_to) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Recursos disponibles en el sistema para reformar');
            $message->cc($user_from);
            $message->replyTo($user_from);
            $message->to($users_to);

        });
    }

    /**
     * Respuesta ajax para enviar informacion para la solicitud de certificacion presupuestaria y CPAC
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sol_cert($id)
    {
        $pac = Pac::where('id', $id)->first();
        $poa = AreaItem::where('id', $pac->area_item_id)->first();
        $codigos = DB::table('area_item as ai')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.id')
            ->where('ai.id', $poa->id)
            ->first();
        return response()->json(["pac" => $pac, "codigos" => $codigos]);

    }

    /**
     * Correo de solicitud de las certicifaciones presupuestaria y/o cpac
     * @param $user
     * @param $pass
     */
    public function sendSolCertificaciones(Request $request)
    {
        $sender = $request->user()->email; //usuario logeado
        $pac_id = $request->input('pac_id_sol');
        $to_presupuestaria = $request->input('user_to_presupuestaria'); //usuario encargado de subir la cert presupuestaria
        $to_cpac = $request->input('user_to_cpac');//usuario encargado generar y  subir la cpac
        $monto_noIVA = $request->input('monto_sol');
        $notas = $request->input('notas_sol');

        $pac = Pac::with('worker')->where('id', $pac_id)->first();
        $poa = AreaItem::where('id', $pac->area_item_id)->first();
        $codigos = DB::table('area_item as ai')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.id')
            ->where('ai.id', $poa->id)
            ->first();
        $user_resp = $pac->worker->email;

        $users_to[] = $to_presupuestaria;//inicialmente siempre tiene el email del usuario de la cert presupuestaria
        if ($pac->proceso_pac == Pac::PROCESO_PAC && ($pac->proceso_pac == Pac::PROCESO_PAC && $pac->procedimiento != 'ÍNFIMA CUANTÍA')) {
            //certificacion presupuestaria y cpac
            $users_to[] = $to_cpac;
        }

        try {

            Mail::send('emails.solicitudes_cpac_presupuestaria', ['pac' => $pac, 'codigos' => $codigos, 'monto' => $monto_noIVA, 'notas' => $notas], function ($message) use ($users_to, $sender, $user_resp) {

                $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
                $message->subject('Solicitud de certificación. Sistema Gestión del POA');
                $message->to($users_to);
                $message->replyTo($sender);
                $message->cc($user_resp);
                $message->getSwiftMessage();
            });

        } catch (\Exception $e) {
//            return redirect()->back()->with(['message_danger' => $e->getMessage()]);
            return redirect()->back()->with(['message_danger' => 'Ocurrio un error al enviar la solicitud, contactar con el administrador del sistema']);
        }

        if (count(Mail::failures()) > 0) {
            $message = 'Se econtraron errores al enviar los correos';
            return redirect()->back()->with(['message_danger' => $message]);

        } else {
            $message = 'Su solicitud fue enviada correctamente';
            return redirect()->back()->with(['message' => $message]);
        }
    }

    /**
     * Subir el pdf de la Certifiacion Presupuestaria
     * @param Request $request
     * @return mixed
     */
    public function postFileCPresup(Request $request)
    {
        $rules = [
            'cpresup-file' => 'bail|required|mimes:pdf|max:100',
            'cod_cpresup' => 'bail|required'
        ];

        $mensajes = [
            'cpresup-file.required' => 'No selecciono ningun archivo',
            'cpresup-file.max' => 'El pdf no puede superar los 100Kb, pruebe con uno mas pequeño',
            'cpresup-file.mimes' => ' El archivo debe ser un pdf',
            'cod_cpresup.required' => 'El código de la certificación es obligatorio'
        ];

        $validator = Validator::make($request->all(), $rules, $mensajes);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->messages()], 422);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $user_upload = $request->user();

        try {

            DB::beginTransaction();

            //pac sobre el que se solicito la cert presup
            $pac = Pac::where('id', $request->input('cpresup_pac_id'))->first();

            //creo y subo una nueva cert presup
            $cpresup = new Cpresupuestaria();
            if ($request->hasFile('cpresup-file')) {
                //elimino archivo anterior
//                    $old_filename = public_path() . '/uploads/pac/certifications/' . $cpac->certificacion;
//                    \File::delete($old_filename);
                //almaceno el nuevo archivo
                $file = $request->file('cpresup-file');
                $name = 'CPresupuestaria' . '' . $cpresup->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/pac/certifications/presupuestaria';
                $file->move($path, $name);
                $cpresup->cert_presup = $name;
            }
            $cpresup->status = Cpresupuestaria::CPRES_ACTIVA;
            $cpresup->cod_cert_presup = $request->input('cod_cpresup');
            $cpresup->pac()->associate($pac);
            $cpresup->user_upload = $user_upload->id;
            $cpresup->save();

            $this->sendCpresupUploadFileMail($pac, $cpresup);


            DB::commit();
            $message = 'Se subió la certificación presupuestaria correctamente. Se envió una notificación por correo al responsable del proceso.';
            if ($request->ajax()) {
                return response()->json(['message' => $message], 200);
            } else {
                return back()->with(['message' => $message]);
            }

        } catch (\Exception $exception) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json(['message_error' => 'Error crítico: ' . $exception->getMessage() . '']);
            } else {
                return back()->with('message_danger', $exception->getMessage());
            }
        }

    }


    /**
     * Descargar el archivo pdf de la certificacion presupuestaria
     * @param $pac_id
     * @return mixed
     */
    public function CPresupDownload(Request $request, $pac_id)
    {
        $pac = Pac::where('id', $pac_id)->first();

        if ($pac) { //existe el pac

            $cpresup = Cpresupuestaria::where('pac_id', $pac->id)->get()->last();

            if (isset($cpresup) && isset($cpresup->cert_presup)) //existe y no es null la certificacion presupuestaria del pac
            {
                $pathtoFile = public_path() . '/uploads/pac/certifications/presupuestaria/' . $cpresup->cert_presup;
                return response()->download($pathtoFile);

            } else {
                return back()->with(['message_danger' => 'NO existe certificaión presupuestaria para el proceso seleccionado.']);
            }
        } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

    }

    /**
     * Correo de notificacion de subida de archivo de certifiacion presupuestaria  a responsable del pac
     * @param $user
     * @param $pass
     */
    public
    function sendCpresupUploadFileMail($pac, $cpresup)
    {
        $user_to = $pac->worker->email; //responsable del pac
        $path = public_path() . '/uploads/pac/certifications/presupuestaria/' . $cpresup->cert_presup;
        $name_file = $cpresup->cert_presup;

        Mail::send('emails.new_cpresup_upload', ['pac' => $pac, 'cpresup' => $cpresup], function ($message) use ($user_to, $path, $name_file) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Certificación Presupuestaria aprobada');
            $message->to($user_to);
            $message->attach($path, ['as' => $name_file, 'mime' => 'application/pdf']);

        });

        if (Mail::failures()) {
            $message = 'Ocurrio un error al enviar la notificación';
            return back()->with(['message_danger' => $message]);
        }
    }


}
