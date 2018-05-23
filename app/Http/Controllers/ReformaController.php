<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Cpac;
use App\Pac;
use App\PacDestino;
use App\PacOrigen;
use App\Programa;
use App\Reforma;
use App\ReformType;
use App\Srpac;
use App\Worker;
use App\InclusionPac;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;

use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class ReformaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('imprimir-reformas')) {

            $reformas = Reforma::from('reformas as r')->with('pac_origen','pac_destino')
                ->join('users as u', 'u.id', '=', 'r.user_id')
                ->join('workers as w', 'w.id', '=', 'u.worker_id')
                ->join('reform_type as rt', 'rt.id', '=', 'r.reform_type_id')
                ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
                ->join('months as m', 'm.cod', '=', 'ai.mes')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('items as i', 'i.id', '=', 'ai.item_id')
                ->join('pac_destino as pd', 'pd.reforma_id', '=', 'r.id')
                ->select('r.id', 'r.estado', 'r.monto_orig', 'rt.tipo_reforma', 'w.nombres', 'w.apellidos', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto', 'a.area', 'ai.area_id as aiID', DB::raw('sum(pd.valor_dest) as total_destino')
                )
                ->orderBy('r.id', 'desc')
                ->groupBy('r.id', 'r.estado', 'r.monto_orig', 'w.nombres', 'w.apellidos', 'mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto', 'a.area')
                ->get();

            return view('reformas.index', compact('reformas'));

        } else return abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Vista para crear Origen de Reformas
     *
     * @return \Illuminate\Http\Response
     */
    public function createReforma(Request $request)
    {
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->month;//1,2,...

        $pac_id = $request->input('to_reform_pac_id');
        $pac = Pac::where('id', $pac_id)->first(); //pac origen sobre el que se realizara la reforma

        //valor acumulado de este pac pendiente en reformas
        $pac_pendiente_total = PacOrigen::where('pac_id', $pac_id)->where('estado', PacOrigen::PACORIGEN_PENDIENTE)->sum('valor_orig');

        //reformas que se dejaron inconclusas
        $pac_origen_test = PacOrigen::select('reforma_id')->where('pac_id', $pac_id)->where('estado', PacOrigen::PACORIGEN_PENDIENTE)->get();
        if (count($pac_origen_test) > 0) { //existen reformas en eeste pac pendientes
            foreach ($pac_origen_test as $pot) {
                $pac_destino_test = PacDestino::where('reforma_id', $pot->reforma_id)->get();
                $message = 'La reforma No.' . $pot->reforma_id . ' del proceso "' . $pac->concepto . '", y no tiene saldo disponible o Ud no terminó una reforma satisfactoriamente, si su caso es este último contacte con un administrador del sistema';
                return redirect()->back()->with('message_danger', $message);
            }
        }

        if ($pac_pendiente_total >= $pac->liberado) {//si el acumulado pendiente es mayo o igual al liberado del pac
            $message = 'Puede que existan reformas pendientes para el item ' . $pac->cod_item . ' del proceso "' . $pac->concepto . '", y no tiene saldo disponible o Ud no terminó una reforma satisfactoriamente, si su caso es este último contacte con un administrador del sistema';
            return redirect()->back()->with('message_danger', $message);
        }

        //usuario logeado
        $user = $request->user();

        $reform_type_id = $request->input('reform_type');
        $reform_type = ReformType::select('tipo_reforma')->where('id', $reform_type_id)->first();

        //Tipo de Reforma
        if (count($reform_type) > 0) {
            $tipo_reforma = $reform_type->tipo_reforma;
        } else {
            return redirect()->back()->with('message_danger', 'Error! no se encontró el tipo de reforma');
        }

        //Poa al que pertenece el PAC
        if (count($pac) > 0) {
            $poa = AreaItem::where('id', $pac->area_item_id)->first();
            $mes_poa_origen = $poa->mes;
        } else {
            return redirect()->back()->with('message_danger', 'Error! no se encontró el PAC');
        }

        //cod_item": "530701" "cod_programa": "55" "cod_actividad": "001" "item": "DESARROMAS INFORMáTICOS" "area_item_id ": 94
        $codigos = DB::table('area_item as ai')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.id', 'i.grupo_gasto')
            ->where('ai.id', $poa->id)
            ->first();

        switch ($tipo_reforma) {

            //Lo realiza solo el responsable del pac, sobre procesos del mismo poa (prog-act-item) y del mismo mes
            case 'INTERNA':

                if (!($user->worker_id == $pac->worker_id || $user->hasRole('root'))) {
                    return redirect()->back()->with('message_danger', 'Solo el dueño del proceso puede realizar las reformas Internas');
                }

                break;

            //Solo la realizan los reponsables de reformas, procesos de la misma act, de mes actual a futuro a mismo mes o pasado a presente
            case 'INFORMATIVA':

                if ($codigos->grupo_gasto == '51' || $codigos->cod_item == '530606') {
                    return redirect()->back()->with('message_danger', 'En el grupo de gasto 51 o item 530606 solo se permiten reformas Ministeriales');
                }

                //descomentar
//                if ($mes_poa_origen > $month ) {
//                    return redirect()->back()->with('message_danger', 'No se admiten movimientos desde el futuro para la Reforma Informativa');
//                }

                if (!($user->hasRole('analista') || $user->hasRole('root'))) {
                    return redirect()->back()->with('message_danger', 'No tiene los permisos necesarios para reformas INFORMATIVA');
                }

                break;

            //Solo la realizan los reponsables de reformas,
            // procesos de la misma act, de mes futuro a presente
            // procesos de la diferentes act, de mes presente a futuro y de futuro a presente
            case 'MINISTERIAL':

                if (!($user->hasRole('analista') || $user->hasRole('root'))) {
                    return redirect()->back()->with('message_danger', 'No tiene los permisos necesarios para reformas MINISTERIAL');
                }

                //descomentar
//                if ($mes_poa_origen < $month ) {
//                    return redirect()->back()->with('message_danger', 'No se admiten movimientos desde el pasado para la Reforma Ministerial');
//                }

                break;
            default:
                return redirect()->back()->with('message_danger', 'Error al definir el tipo de reforma');
        }

        //procesos con saldo liberado en este poa para tener en cuenta en la reforma para los pac de origen
        $pacs_all = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'p.proceso_pac', 'i.cod_programa', 'i.cod_actividad', 'i.grupo_gasto', 'p.area_item_id', 'a.area', 'p.liberado')
            ->where('p.area_item_id', $poa->id)
            ->where('p.liberado', '>', 0)//que tengan liberado para reformar
            ->get();

        $pacs_all = collect($pacs_all);//convierto a colleccion para poder utilizar map()

        // recorro cada elemento de la coleccion para agregar un nuevo elemento donde indico si tiene srpac.pdf
        $pacs_all->map(function ($pac) {
            $srpac = Srpac::where('pac_id', $pac->id)->select('solicitud_file', 'status')->get()->last(); //ultimo pdf de Srpac subido
            $sol_rpac = null;
            $sol_rpac_status = null;
            if ($srpac) { //si tiene un pdf asignado a la srpac lo agrego
                $sol_rpac = $srpac->solicitud_file;
                $sol_rpac_status = $srpac->status;
            }
            $pac->srpac_file = $sol_rpac; //archivo srpac
            $pac->srpac_status = $sol_rpac_status; //status archivo srpac

            //valore totale pendiente de este pac por reformar
            $pac_pendiente_total = PacOrigen::where('pac_id', $pac->id)->where('estado', PacOrigen::PACORIGEN_PENDIENTE)->sum('valor_orig');
            //valor que se puede tomar para la reforma, sera el liberado menos lo pendiente
            $pac->valor_reformar_origen = $pac->liberado - $pac_pendiente_total;

            return $pac;
        });

        // filtro la colleccion y solo dejo las que tienen  srpac_file subido y activo para enviarlas a la vista del origen, o sino es un proceso pac y que tenga valor disponible para ser reformado
        $pacs = $pacs_all->filter(function ($value, $key) {
            if (((isset($value->srpac_file) && $value->srpac_status == Srpac::SRPAC_ACTIVA) || $value->proceso_pac == Pac::NO_PROCESO_PAC) && $value->valor_reformar_origen > 0) {
                return true;
            } else return false;
        });

        //valor maximo disponible para tomar en la reforma
//        $poa_disponible = Pac::where('area_item_id', $poa->id)->where('liberado', '>', 0)->sum('liberado');
        $poa_disponible = $pacs->sum('valor_reformar_origen');

        return view('reformas.createReforma', compact('pacs', 'poa_disponible', 'codigos', 'month', 'valor_mes', 'tipo_reforma', 'reform_type_id'));

    }


    /**
     * Guarda el ORIGEN de la REFORMA
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_login = $request->user();

        try {
            DB::beginTransaction();

            $area_item_id = $request->input('area_item_origen');
            $poafdg = AreaItem::where('id', $area_item_id)->first();//poa de origen de la reforma


            $pac_id = $request->input('pac_id');//arreglo de los pacs de origen de este poa
            $valor_orig = $request->input('subtotal_id');//arreglo con los valores de este poa
            $total = $request->input('total_origen');//total para la reforma
            $disponible = $request->input('disponible');//max valor que se puede tomar de este poa, liberado
            $justificativo_origen = $request->input('informe');
            $reform_type_id = $request->input('reform_type_id');
            $tipo_reforma = ReformType::where('id', $reform_type_id)->first();

            if ($tipo_reforma->tipo_reforma != 'INTERNA' && $justificativo_origen == '') {
                $message = 'La justificación de la reforma es obligatoria';
                return redirect()->back()->withInput()->with('message_danger', $message);
            }

            //el monto para la reforma no puede ser mayor que lo que tiene el item sin ejecutar          
            if ($total > $disponible) {
                $message = 'No puede tomar un valor mayor a lo disponible';
                return redirect()->back()->withInput()->with('message_danger', $message);
            } else $monto_orig = $total;

            //el valor total de las reformas que estan pendiente sobre este item
//            $reformado_pendiente = Reforma::where('area_item_id', $area_item_id)->where('estado',Reforma::REFORMA_PENDIENTE)->sum('monto_orig');
            //lo que se puede reformar segun lo que hay pendiente de aprobacion
//            $por_reformar = $disponible - $reformado_pendiente;

//            if ($total > $reformado_pendiente) {
//                $message = 'Existen Reformas pendientes sobre este poa, solo hay disponible $ ' . $por_reformar . ' para reformar';
//                return redirect()->back()->withInput()->with('message_danger', $message);
//            }

            $reforma = new Reforma();
            $reforma->area_item()->associate($poafdg);
            $reforma->reform_type()->associate($tipo_reforma);
            $reforma->user_id = $user_login->id;
            $reforma->monto_orig = $monto_orig;
            $reforma->estado = Reforma::REFORMA_PENDIENTE;
            $reforma->nota = trim($justificativo_origen);
            $reforma->save();

            //guardar los  pac origen
            $cont = 0;
            while ($cont < count($pac_id)) {

                $pac = Pac::where('id', $pac_id[$cont])->first(); //pac origen

                //comprobar la disponibilidad del dinero del pac comprobando si hay reformas sobre este pac pendientes
                $pac_pendiente = PacOrigen::where('pac_id', $pac_id[$cont])->where('estado', PacOrigen::PACORIGEN_PENDIENTE)->sum('valor_orig');
                $pendiente = $pac->liberado - $pac_pendiente;
                // if ($valor_orig[$cont] > $pendiente )  {
                //     $message = 'Existen reformas pendientes. El item ' . $pac->cod_item . ' por concepto ' . $pac->concepto . ', solo tiene disponible $ ' . $pendiente . ' para reformar ';
                //     return redirect()->back()->withInput()->with('message_danger', $message);
                // }
                //guardo el pac de origen, estado por defecto Pendiente
                $pac_origen = new PacOrigen();
                $pac_origen->reforma()->associate($reforma);
                $pac_origen->pac_id = $pac_id[$cont];
                $pac_origen->valor_orig = $valor_orig[$cont];
                $pac_origen->estado = PacOrigen::PACORIGEN_PENDIENTE;
                $pac_origen->save();
                $cont++;
            }

            DB::commit();
            //devolver valores necesarios para reforma destino
            return redirect()->route('destinoReforma', ["reforma_id" => $reforma->id, "pacs_origen_id" => $pac_id]);
            //return response()->json(["monto_reforma" => $reforma->monto_orig, "reforma_id" => $reforma->id, "pacs_origen_id" => $pac_id]);
        } catch (\Exception $e) {
            DB::rollback();
//            return response()->json(["response" => $e->getMessage(), "tipo" => "error"]);
            //$message = $e->getMessage();
            $message = "Ha ocurrido un error al guardar los registros en la base de datos";
            return redirect()->back()->withInput()->with('message_danger', $message);
        }
    }

    /**
     * Vista para crear Destino de Reformas
     *
     * @return \Illuminate\Http\Response
     */
    public function destino(Request $request)
    {
        $user_login = $request->user();

        $fecha_actual = Carbon::now();
//        $month = $fecha_actual->formatLocalized('%B');//ENERO, FEBRERO, etc
        $month = $fecha_actual->month;//1,2,...

        //arreglo con los pac de origen del poa
        $array_pacs_origen_id = $request->input('pacs_origen_id');
        $reforma_id = $request->input("reforma_id");

        $reforma = Reforma::where('id', $reforma_id)->with('pac_origen', 'reform_type', 'area_item')->first();

        if (count($reforma) > 0) {
            //Tipo de Reforma
            $tipo_reforma = $reforma->reform_type->tipo_reforma;
        } else {
            return redirect()->back()->with('message_danger', 'Error! no se encontró el tipo de reforma');
        }

        //Poa_FDG origen sobre el que se hace la reforma:  "item_id" => 17 "area_id" => 2 "monto" => "1400.00" "mes" => 2
        $poa = $reforma->area_item;
        $cod_actividad_origen = $poa->item->cod_actividad;
        $mes_poa_origen = $poa->mes;

        //mostrar los pac que apareceran en el destino
        $pacs = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'p.mes as codmes', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.liberado', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'p.proceso_pac', 'i.cod_programa', 'i.cod_actividad', 'i.grupo_gasto', 'p.area_item_id', 'p.inclusion');

        switch ($tipo_reforma) {

            //Lo realiza solo el responsable del pac, sobre procesos del mismo poa (prog-act-item) y del mismo mes
            case 'INTERNA':
                $pacs = $pacs
                    ->whereNotIn('p.id', $array_pacs_origen_id)//no mostrar los pac de los k estoy quitando dinero
                    //Descomentar
//                    ->where('p.mes', '=', $month)//solo pacs del mes actual
                    ->where('p.area_item_id', '=', $poa->id)//muestra los pac que pertenecen al poa de donde estoy kitando dinero
//                    ->where('p.worker_id', '=', $user_login->id)//muestra los pac del trabajador logueado
                    ->get();
                break;

            //Solo la realizan los reponsables de reformas, procesos de la misma act, de mes actual a futuro o a mismo mes, y de  pasado a presente
            case 'INFORMATIVA':
                $pacs = $pacs
                    ->whereNotIn('p.id', $array_pacs_origen_id)//no mostrar los pac de los k estoy quitando dinero
                    ->where('i.grupo_gasto', '!=', '51')//no mostrar del grupo gasto 51 solo para ministerial
                    ->where('p.cod_item', '!=', '530606')//el item 530606 solo para ministerial
                    //Descomentar
                    //si mes del origen = mes actual => origen=presente
//                    ->when($mes_poa_origen==$month,function ($query) use ($cod_actividad_origen,$mes_poa_origen,$array_pacs_origen_id){
                    // misma actividad de mes actual a futuro o a mismo mes, destino>=origen(presente,futuro)
//                        return $query->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','>=',$mes_poa_origen)
                    //o no son procesos pac y de la misma actividad al mes actual o futuro y que no sean los del origen
//                            ->orWhere(function($query) use ($cod_actividad_origen,$mes_poa_origen,$array_pacs_origen_id){
//                                $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','>=',$mes_poa_origen)->whereNotIn('p.id', $array_pacs_origen_id);
//                        });
//                    })

                    //Descomentar
                    //si mes_origen < mes_actual => origen=pasado
//                    ->when($mes_poa_origen<$month, function ($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id) {
                    // misma actividad de pasado a presente, destino = presente
//                        return $query->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','=',$month)
                    //o no son procesos pac y de la misma actividad al mes actual y que no sean los del origen
//                            ->orWhere(function($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id){
//                            $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','=',$month)->whereNotIn('p.id', $array_pacs_origen_id);
//                        });
//                    })

                    //****************Eliminar despues de descomentar
                    ->where('i.cod_actividad', '=', $cod_actividad_origen)
                    ->orWhere(function ($query) use ($cod_actividad_origen, $array_pacs_origen_id) {
                        $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad', '=', $cod_actividad_origen)->whereNotIn('p.id', $array_pacs_origen_id);
                    })
                    //*********************
                    ->get();
                break;

            //Solo la realizan los reponsables de reformas,
            // procesos de la misma act, de mes futuro a presente
            // procesos de la diferentes act, de mes presente a futuro y de futuro a presente
            case 'MINISTERIAL':
                $pacs = $pacs
                    ->whereNotIn('p.id', $array_pacs_origen_id)//no mostrar los pac de los k estoy quitando dinero
                    //Descomentar
                    //si mes del origen > mes actual => origen=futuro
//                    ->when($mes_poa_origen>$month,function ($query) use ($cod_actividad_origen,$mes_poa_origen,$array_pacs_origen_id,$month){
                    // misma actividad de mes futuro a mes presente, destino<origen=presente
//                        return $query->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','=',$month)
                    //o no son procesos pac y de la misma actividad al mes actual desde futuro y que no sean los del origen
//                            ->orWhere(function($query) use ($cod_actividad_origen,$mes_poa_origen,$array_pacs_origen_id,$month){
//                                $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad','=',$cod_actividad_origen)->where('p.mes','=',$month)->whereNotIn('p.id', $array_pacs_origen_id);
//                            });
//                    })
                    //Descomentar
                    //si mes_origen = mes_actual => origen=presente
//                    ->when($mes_poa_origen==$month, function ($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id) {
                    // diferente actividad de presente a futuro destino>origen
//                        return $query->where('i.cod_actividad','<>',$cod_actividad_origen)->where('p.mes','>',$month)
                    //o no son procesos pac y de diferente actividad al mes actual y que no sean los del origen
//                            ->orWhere(function($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id){
//                                $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad','<>',$cod_actividad_origen)->where('p.mes','>',$month)->whereNotIn('p.id', $array_pacs_origen_id);
//                            });
//                    })
                    //Descomentar
                    //si mes_origen > mes_actual => origen=futuro
//                    ->when($mes_poa_origen>$month, function ($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id) {
                    // diferente actividad de futuro a presente destino<origen
//                        return $query->where('i.cod_actividad','<>',$cod_actividad_origen)->where('p.mes','=',$month)
                    //o no son procesos pac y de diferente actividad al mes actual y que no sean los del origen
//                            ->orWhere(function($query) use ($cod_actividad_origen,$month,$array_pacs_origen_id){
//                                $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->where('i.cod_actividad','<>',$cod_actividad_origen)->where('p.mes','=',$month)->whereNotIn('p.id', $array_pacs_origen_id);
//                            });
//                    })

                    //***********Eliminar despues de descomentar
                    ->orWhere(function ($query) use ($cod_actividad_origen, $array_pacs_origen_id) {
                        $query->where('p.proceso_pac', Pac::NO_PROCESO_PAC)->whereNotIn('p.id', $array_pacs_origen_id);
                    })
                    //**************************
                    ->get();
                break;
        }

        //convierto a colleccion para poder utilizar map()
        $pacs_coll = collect($pacs);

        //recorro cada elemento de la coleccion para agregar un nuevo elemento donde indico si tiene srpac.pdf
        $pacs_coll->map(function ($pac) {
            $srpac = Srpac::where('pac_id', $pac->id)->select('solicitud_file', 'status')->get()->last(); //ultimo pdf de Srpac subido
            $inclupac = InclusionPac::where('pac_id', $pac->id)->select('inclusion_file', 'status')->get()->last(); //ultimo pdf de inclusion pac subido
            $sol_rpac = null;
            $sol_rpac_status = null;
            $incl_pac_file = null;
            $incl_pac_status = null;
            if ($srpac) { //si tiene un pdf asignado a la srpac lo agrego
                $sol_rpac = $srpac->solicitud_file;
                $sol_rpac_status = $srpac->status;
            }
            if ($inclupac) { //si tiene un pdf asignado a la inclusion pac lo agrego
                $incl_pac_file = $inclupac->inclusion_file;
                $incl_pac_status = $inclupac->status;
            }
            $pac->srpac_file = $sol_rpac; //archivo srpac
            $pac->srpac_status = $sol_rpac_status; //status archivo srpac
            $pac->inclusion_file = $incl_pac_file; //archivo  inclusion pac
            $pac->inclusion_file_status = $incl_pac_status; //status archivo  inclusion pac
            return $pac;
        });

        //filtro la colleccion y solo dejo las que tienen  srpac_file para enviarlas a la vista del destino, o no es proceso pac o es inclusion esta el archivo de inclusion subido y activo
        $pacs_All = $pacs_coll->filter(function ($value, $key) {
            if (
                (isset($value->srpac_file) && $value->srpac_status == Srpac::SRPAC_ACTIVA) ||
                $value->proceso_pac == Pac::NO_PROCESO_PAC ||
                ($value->inclusion == Pac::PROCESO_INCLUSION_SI
//                    && !is_null($value->inclusion_file) && $value->inclusion_file_status==\App\InclusionPac::INCLUSION_PAC_ACTIVA
                )
            ) {
                return true;
            } else return false;
        });

        //valor maximo disponible para tomar en la reforma
        $poa_disponible = Pac::where('area_item_id', $poa->id)->where('liberado', '>', 0)->sum('liberado');

        return view('reformas.destino', compact('poa_disponible', 'reforma', 'pacs_All', 'month', 'tipo_reforma', 'poa'));

    }


    /**
     * Guardar los pacs de destino de la reforma, actualizar datos en reforma para informe tecnico enviandolo por correo
     * y redirigir a la pagina de listar reformas
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storePacsDestino(Request $request)
    {
        try {
            DB::beginTransaction();

            $fecha_actual = Carbon::now();
            $month = $fecha_actual->formatLocalized('%B');//mes en español

            $monto_reforma = $request->input('monto_reforma');
            $por_distribuir = $request->input('por_distribuir');
            $total_destino = $request->input('total_destino');
            $justificativo = $request->input('justificativo_destino'); //arreglo justificativos para el informe tecnico de la reforma
            $pac_id = $request->input('pac_idd');//arreglo de los pac_id de destino
            $valor_dest = $request->input('subtotal_idd');//arreglo con los valores de pac de destino

            $reforma = Reforma::where('id', $request->input("reforma_id"))->with('pac_origen', 'reform_type', 'area_item', 'user')->first();

            $tipo_reforma = $reforma->reform_type->tipo_reforma;

            //id poa de origen de la reforma
            $poa_origen_id = $reforma->area_item_id;

            //Poa_FDG sobre el que se hace la reforma:  "item_id" => 17 "area_id" => 2 "monto" => "1400.00" "mes" => 2
            $poa = $reforma->area_item;
            $cod_actividad_origen = $poa->item->cod_actividad;
            $cod_item_origen = $poa->item->cod_item;
            $area_id_origen = $poa->area_id;
            $mes_origen = $poa->mes;

            if ($por_distribuir > 0 || $monto_reforma != $total_destino) {
                $message = 'Debe distribuir todo el dinero de la reforma';
                return response()->json(["message" => $message, "tipo" => 'error']);
            }

            $pacs_origen = PacOrigen::where('reforma_id', $reforma->id)->with('pac')->get();  //coleccion con los pacs del origen

            //Esto es en caso de que el poa sea compartido y el responsable sea de otra area
            $proceso_de_su_area = 'no';
            foreach ($pacs_origen as $po) {
                //si el proceso de origen pertenece a un trabajador del area del analista
                if (Auth::user()->worker->departamento->area_id == $po->pac->worker->departamento->area->id) {
                    $proceso_de_su_area = 'si';
                    break;
                }
            }

            switch ($tipo_reforma) {

                case 'INTERNA':

                    $cont = 0;
                    $pac_control = [];
                    while ($cont < count($pac_id)) {

                        //no permitir agregar recurso dos veces en el mismo item
                        if (in_array($pac_id[$cont], $pac_control)) {
                            $message = 'Optimice su reforma, ha sumado dos valores en un mismo item';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }
                        $pac_control[] = $pac_id[$cont];

                        $pac = Pac::where('id', $pac_id[$cont])->with('area_item')->first();//pac destino


                        if ($pac->area_item_id != $poa_origen_id) {
                            $message = 'En la reforma INTERNA solo se admiten como destino los items del mismo origen';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        if (Auth::user()->id != $reforma->user_id || Auth::user()->worker_id != $pac->worker_id) {
                            $message = 'En la reforma INTERNA solo se admiten items propios del usuario ';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        if ($pac->area_item->item->grupo_gasto == '51' || $pac->cod_item == '530606') {
                            $message = 'No se permiten reformas INTERNAS para el Grupo de Gasto 51 ni la partida 530606';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        $pac_dest = new PacDestino();
                        $pac_dest->reforma()->associate($reforma);
                        $pac_dest->pac_id = $pac_id[$cont];
                        $pac_dest->valor_dest = $valor_dest[$cont];
                        $pac_dest->save();

                        $cont++;
                    }

                    //*** Reforma interna no necesita aprobacion, actualizar valores al mismo tiempo k se guarda ***//

                    //suma de todos los pac destino, puede ser mas de un poa
                    $monto_destino_total = PacDestino::where('reforma_id', $reforma->id)->sum('valor_dest');

                    //actualizar los montos de cada poafdg destino (area_item) y los pac
                    foreach ($reforma->pac_destino as $pac_dest) {
                        $pac = Pac::where('id', $pac_dest->pac_id)->first();
                        $poa_dest = $pac->area_item; //objeto, relacion belongsTo
                        $poa_dest->monto = $poa_dest->monto + $pac_dest->valor_dest;
                        $poa_dest->update();

                        //sumar los valores al pac destino
                        $pac->presupuesto = $pac->presupuesto + $pac_dest->valor_dest;
                        $pac->disponible = $pac->disponible + $pac_dest->valor_dest; //el valor agregado en la reforma pasa a estar disponible
                        $pac->update();

                        $srpac = Srpac::where('pac_id', $pac_dest->pac_id)->get()->last(); //ultimo pdf de Srpac subido
                        $srpac->status = Srpac::SRPAC_INACTIVA; //deshabilito el archivo de srpac para que se pueda generar nuevamente otro archivo
                        $srpac->update();
                    }

                    //actualizar el monto del poafdg origen  (area_item), poa al que se le quitara saldo
                    $poaorigen = $reforma->area_item; // objeto, relacion belongsTo
                    $poaorigen_actual = ($poaorigen->monto) - ($reforma->monto_orig);
                    $poaorigen->monto = $poaorigen_actual;
                    $poaorigen->update();

                    //actualizar valores de pacs origen
                    foreach ($reforma->pac_origen as $pac_orig) {
                        $pac = Pac::where('id', $pac_orig->pac_id)->first();
                        $pac->presupuesto = $pac->presupuesto - $pac_orig->valor_orig;
                        $pac->liberado = $pac->liberado - $pac_orig->valor_orig;
                        $pac->update();

                        $pac_orig->estado = PacOrigen::PACORIGEN_APROBADA;
                        $pac_orig->update();

                        $srpac = Srpac::where('pac_id', $pac_orig->pac_id)->get()->last(); //ultimo pdf de Srpac subido
                        $srpac->status = Srpac::SRPAC_INACTIVA; //deshabilito el archivo de srpac para que se pueda generar nuevamente otro archivo
                        $srpac->update();
                    }

                    $reforma->estado = Reforma::REFORMA_APROBADA;
                    $reforma->update();

                    if ($reforma->monto_orig != $monto_destino_total) {
                        $message = 'Los montos del origen ($' . $reforma->monto_orig . ') y destino ($' . $monto_destino_total . ') no coinciden.';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    break;

                case 'INFORMATIVA':

                    //Si el usuario logueado pertenece al area del poa de origen o si es un poa compartido con su area y es el analista de reformas de esa area; o root, administrador--}}
                    if ( ( (Auth::user()->worker->departamento->area_id == $area_id_origen || $proceso_de_su_area=='si') && Auth::user()->hasRole('analista') ) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador'))) {
                        $autorizado = true;
                    } else {
                        $message = 'NO tiene permisos para realizar la reforma informativa';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    $cont = 0;
                    $pac_control = [];
                    while ($cont < count($pac_id)) {

                        if ($justificativo[$cont] == '') {
                            $message = 'Debe detallar la justificación de la reforma en cada destino';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        //no permitir quitar recurso dos veces en el mismo item
                        if (in_array($pac_id[$cont], $pac_control)) {
                            $message = 'Optimice su reforma, ha sumado dos valores en un mismo item';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        $pac_control[] = $pac_id[$cont];

                        $pac = Pac::where('id', $pac_id[$cont])->with('area_item')->first();//pac destino

                        //solo se permite esta reforma destinos con la misma actividad del origen
                        if ($pac->area_item->item->cod_actividad != $cod_actividad_origen) {
                            $message = 'En la reforma INFORMATIVA solo se admiten como destino los items poa de la misma actividad';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        //Grupo gasto 51 solo para ministerial
                        if ($pac->area_item->item->grupo_gasto == '51' || $pac->cod_item == '530606') {
                            $message = 'No se permiten reformas INFORMATIVAS para el Grupo de Gasto 51, o el item 530606';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        $pac_dest = new PacDestino();
                        $pac_dest->reforma()->associate($reforma);
                        $pac_dest->pac_id = $pac_id[$cont];
                        $pac_dest->valor_dest = $valor_dest[$cont];
                        $pac_dest->justificativo = $justificativo[$cont];
                        $pac_dest->save();

                        $cont++;

                    }

                    //*** Reforma INFORMATIVA necesita aprobacion, no se pueden actualizar valores, enviar el informe tecnico ***//

                    $pacdestino = PacDestino::where('reforma_id', $reforma->id)->with('pac')->first();
                    $poa_dest = AreaItem::where('id', $pacdestino->pac->area_item_id)->with('item')->first();


                    //Tipo de informe:
                    $tipo_informe = null;
                    // Distintos Items del mismo mes: Reforma
                    if ($cod_item_origen != $poa_dest->item->cod_item && $mes_origen == $poa_dest->mes) {
                        $tipo_informe = 'Reforma';
                    }
                    // Mismos Items distintos meses:Reprogramación
                    if ($cod_item_origen == $poa_dest->item->cod_item && $mes_origen != $poa_dest->mes) {
                        $tipo_informe = 'Reprogramación';
                    }
                    //Distintos Items distintos meses: Reforma/Reprogramación
                    if ($cod_item_origen != $poa_dest->item->cod_item && $mes_origen != $poa_dest->mes) {
                        $tipo_informe = 'Reforma/Reprogramación';
                    }

                    //Codigo  de informe:
                    $cod_informe = null;
                    $num_min = null;
                    $num_modif = null;
                    //Movimiento poa entre actividades diferentes: MIN
                    if ($cod_actividad_origen != $poa_dest->item->cod_actividad) {
                        $cod_informe = 'MIN';
                        $num_min = DB::table('reformas')->max('num_min') + 1;
                    }
                    //Movimiento poa entre actividades iguales: MODIF
                    if ($cod_actividad_origen === $poa_dest->item->cod_actividad) {
                        $cod_informe = 'MODIF';
                        $num_modif = DB::table('reformas')->max('num_modif') + 1;
                    }

                    $reforma->tipo_informe = $tipo_informe;
                    $reforma->cod_informe = $cod_informe;
                    $reforma->num_min = $num_min;
                    $reforma->num_modif = $num_modif;
                    $reforma->estado = Reforma::REFORMA_PENDIENTE;
                    $reforma->update();

                    $area_id = $reforma->area_item->area_id;

                    //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
                    $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                        $query->where('area_id', $area_id)
                            ->where('departamento', 'like', "%direcc%");
                    })->first();

                    $pac_destino = PacDestino::with('pac')->where('reforma_id', $reforma->id)->first(); //para buscar poa destino

                    $pdf = PDF::loadView('reformas.informeT-pdf', compact('reforma', 'jefe_area', 'fecha_actual', 'month', 'pac_destino'))->stream();

                    $this->sendInfoTecReformaMail($reforma, $pdf);

                    break;

                case 'MINISTERIAL':
                    //Si el usuario logueado pertenece al area del poa de origen o si es un poa compartido con su area y es el analista de reformas de esa area; o root, administrador--}}
                    if ( ( (Auth::user()->worker->departamento->area_id == $area_id_origen || $proceso_de_su_area=='si') && Auth::user()->hasRole('analista') ) || (Auth::user()->hasRole('root') || Auth::user()->hasRole('administrador'))) {
                        $autorizado = true;
                    } else {
                        $message = 'NO tiene permisos para realizar la reforma informativa';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    $cont = 0;
                    $pac_control = [];
                    while ($cont < count($pac_id)) {

                        if ($justificativo[$cont] == '') {
                            $message = 'Debe detallar la justificación de la reforma en cada destino';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }

                        //no permitir quitar recurso dos veces en el mismo item
                        if (in_array($pac_id[$cont], $pac_control)) {
                            $message = 'Optimice su reforma, ha sumado dos valores en un mismo item';
                            return response()->json(["message" => $message, "tipo" => 'error']);
                        }
                        $pac_control[] = $pac_id[$cont];

                        $pac_dest = new PacDestino();
                        $pac_dest->reforma()->associate($reforma);
                        $pac_dest->pac_id = $pac_id[$cont];
                        $pac_dest->valor_dest = $valor_dest[$cont];
                        $pac_dest->justificativo = $justificativo[$cont];
                        $pac_dest->save();

                        $cont++;
                    }

                    //*** Reforma MINISTERIAL necesita aprobacion, no se pueden actualizar valores, enviar el informe tecnico ***//

                    $pacdestino = PacDestino::where('reforma_id', $reforma->id)->with('pac')->first();
                    $poa_dest = AreaItem::where('id', $pacdestino->pac->area_item_id)->with('item')->first();

                    //Tipo de informe:
                    $tipo_informe = null;
                    // Distintos Items del mismo mes: Reforma
                    if ($cod_item_origen != $poa_dest->item->cod_item && $mes_origen == $poa_dest->mes) {
                        $tipo_informe = 'Reforma';
                    }
                    // Mismos Items distintos meses:Reprogramación
                    if ($cod_item_origen == $poa_dest->item->cod_item && $mes_origen != $poa_dest->mes) {
                        $tipo_informe = 'Reprogramación';
                    }
                    //Distintos Items distintos meses: Reforma/Repregramación
                    if ($cod_item_origen != $poa_dest->item->cod_item && $mes_origen != $poa_dest->mes) {
                        $tipo_informe = 'Reforma/Reprogramación';
                    }

                    //Codigo  de informe:
                    $cod_informe = null;
                    $num_min = null;
                    $num_modif = null;
                    //Movimiento poa entre actividades diferentes: MIN
                    if ($cod_actividad_origen != $poa_dest->item->cod_actividad) {
                        $cod_informe = 'MIN';
                        $num_min = DB::table('reformas')->max('num_min') + 1;
                    }
                    //Movimiento poa entre actividades iguales: MODIF
                    if ($cod_actividad_origen === $poa_dest->item->cod_actividad) {
                        $cod_informe = 'MODIF';
                        $num_modif = DB::table('reformas')->max('num_modif') + 1;
                    }

                    $reforma->tipo_informe = $tipo_informe;
                    $reforma->cod_informe = $cod_informe;
                    $reforma->num_min = $num_min;
                    $reforma->num_modif = $num_modif;
                    $reforma->estado = Reforma::REFORMA_PENDIENTE;
                    $reforma->update();

                    $area_id = $reforma->area_item->area_id;

                    //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
                    $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                        $query->where('area_id', $area_id)
                            ->where('departamento', 'like', "%direcc%");
                    })->first();

                    $pac_destino = PacDestino::with('pac')->where('reforma_id', $reforma->id)->first(); //para buscar poa destino

                    $pdf = PDF::loadView('reformas.informeT-pdf', compact('reforma', 'jefe_area', 'fecha_actual', 'month', 'pac_destino'))->stream();

                    $this->sendInfoTecReformaMail($reforma, $pdf);

                    break;
            }

            DB::commit();
            if (Auth::user()->can('imprimir-reformas')) {
                return response()->json(["message" => "Reforma creada correctamente", "tipo" => 'listar_reformas']);
            } else return response()->json(["message" => "Reforma creada correctamente", "tipo" => 'listar_procesos']);
        } catch (\Exception $e) {
            DB::rollback();
            $reforma = Reforma::where('id', $request->input('reforma_id'))->first();
            $reforma->delete();
            return response()->json(["message" => $e->getMessage(), "tipo" => "error_critico"]);
//            return response()->json(["message" => "Lo sentimos, ha ocurrido un error interno, recargue la pagina y comience la reforma nuevamente", "tipo" => "error_critico"]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reforma = DB::table('reformas as r')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto')
            ->where('r.id', $id)->first();

        $detalles_o = DB::table('reformas as r')
            ->join('pac_origen as po', 'po.reforma_id', '=', 'r.id')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('pacs as pac', 'pac.id', '=', 'po.pac_id')
            ->join('workers as w', 'w.id', '=', 'pac.worker_id')
            ->select('po.valor_orig', 'pac.concepto', 'w.nombres', 'w.apellidos')
            ->where('r.id', $id)
            ->get();

        $detalles_d = DB::table('reformas as r')
            ->join('pac_destino as pd', 'pd.reforma_id', '=', 'r.id')
            ->join('pacs as pac', 'pac.id', '=', 'pd.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
            ->join('area_item as ai', 'ai.id', '=', 'pac.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'pac.worker_id')
            ->select('pd.valor_dest', 'pac.concepto', 'm.month as mes', 'pac.item', 'w.nombres', 'w.apellidos', 'i.cod_actividad', 'i.cod_programa', 'i.cod_item')
            ->where('r.id', $id)
            ->get();

        return view('reformas.detalleReforma', compact('reforma', 'detalles_o', 'detalles_d'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }


    /**
     * Aprobar las reformas Informtivas y Ministerial
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, $id)
    {

        $reforma = Reforma::findOrFail($id);

        $poaorigen = AreaItem::where('id', $reforma->area_item_id)->first();//poa al que se le quitara saldo

        $monto_destino_total = PacDestino::where('reforma_id', $reforma->id)->sum('valor_dest'); //suma de todos los pac destino, puede ser mas de un poa (area_item)

        //actualizar los montos de cada poafdg destino (area_item) y los pac_destino
        foreach ($reforma->pac_destino as $pac_dest) {
            $pac = Pac::where('id', $pac_dest->pac_id)->first();
            $poa_dest = $pac->area_item;
            $poa_dest->monto = $poa_dest->monto + $pac_dest->valor_dest;
            $poa_dest->update();

            $srpac = Srpac::with('srpac_destino')->where('pac_id', $pac_dest->pac_id)->get()->last(); //ultimo pdf de Srpac subido
            if (count($srpac) > 0) {
                $srpac->status = Srpac::SRPAC_INACTIVA; //pasa a estado inactivo
                $srpac->update();
            }

            //sumar los valores al pac destino
            $pac->presupuesto = $pac->presupuesto + $pac_dest->valor_dest;
            $pac->disponible = $pac->disponible + $pac_dest->valor_dest;

            //si era una inclusion lo convierto en un proceso normal
            if ($pac->inclusion == Pac::PROCESO_INCLUSION_SI) {
                $pac->inclusion = Pac::PROCESO_INCLUSION_NO;
            }
            $pac->srpac = Pac::NO_APROBADA_SRPAC; //srpac=0
            $pac->update();
        }

        //actualizar el monto del poafdg origen  (area_item), poa al que se le quitara saldo
        $poaorigen = $reforma->area_item;
        $poaorigen_actual = ($poaorigen->monto) - ($reforma->monto_orig);
        $poaorigen->monto = $poaorigen_actual;
        $poaorigen->update();

        //actualizar valores de pacs origen
        foreach ($reforma->pac_origen as $pac_orig) {
            $pac = Pac::where('id', $pac_orig->pac_id)->first();

            $srpac = Srpac::with('srpac_destino')->where('pac_id', $pac_orig->pac_id)->get()->last(); //ultimo pdf de Srpac subido
            if (count($srpac) > 0) {
                $srpac->status = Srpac::SRPAC_INACTIVA; //deshabilito el archivo de srpac para que se pueda generar nuevamente otro archivo
                $srpac->update();
            }
            //restar los valores al pac origen
            $pac->presupuesto = $pac->presupuesto - $pac_orig->valor_orig;
            $pac->liberado = $pac->liberado - $pac_orig->valor_orig;
            $pac->srpac = Pac::NO_APROBADA_SRPAC;//estado inicial
            $pac->update();
            $pac_orig->estado = PacOrigen::PACORIGEN_APROBADA;
            $pac_orig->update();
        }

        $reforma->estado = Reforma::REFORMA_APROBADA;
        $reforma->update();

        if ($reforma->monto_orig != $monto_destino_total) {
            $message = 'Los montos del origen ($' . $reforma->monto_orig . ') y destino ($' . $monto_destino_total . ') no coinciden.';
            return response()->json(["message" => $message, "tipo" => 'error']);
        }

        $accion = 'aprobada';
        $this->sendReformaStatusMail($reforma, $accion);

        $message = "Reforma aprobada";
        if ($request->ajax()) {
            return response()->json(["message" => $message]);
        }
        return redirect()->route('admin.reformas.index');
    }


    /**
     * Eliminar la reforma, o cancelar en el momento de agregra destino
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $reforma = Reforma::findOrFail($id);

        $accion = 'eliminada';

        if ($request->exists('cancelada')) { //no enviar email si fue cancelada durante el proceso
            $reforma->delete();
        } else {
            $this->sendReformaStatusMail($reforma, $accion);
            $reforma->delete();
        }

        $message = 'eliminada';
        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
    }


    /**
     * Correo de notificacion del informe técnico de reforma enviado a Blanca Silva y al usuario que solicita la reforma
     * @param $user
     * @param $pass
     */
    public function sendInfoTecReformaMail($reforma, $pdf)
    {
        $user_to = 'blanca.silva@fedeguayas.com.ec';
        $user_sol = $reforma->user->email; //usuario que solicita la reforma
        $data = [];

        Mail::send('emails.informe_tecnico_reforma', [$data], function ($message) use ($user_to, $user_sol, $pdf) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Informe Técnico de Reforma');
            $message->cc($user_sol);
            $message->to($user_to);
            $message->attachData($pdf, 'informe_reforma.pdf', ['mime' => 'application/pdf']);

        });

        if (Mail::failures()) {
            $message = 'Ocurrio un error al enviar el informe técnico';
            return response()->json(["message" => $message, "tipo" => 'error']);
//            return back()->with(['message_danger' => 'Ocurrio un error al enviar el informe técnico']);

        }
    }

    /**
     * Correo de notificacion de estado de la reforma, aprobada o cancelada, enviado al usuario que solicito la reforma
     * @param $user
     * @param $pass
     */
    public function sendReformaStatusMail($reforma, $accion)
    {
        //area a la que pertenece el trabajador que solicito la reforma
        $area_id = $reforma->user->worker->departamento->area->id;

        //trabajadores que pertenecen al mismo area del trabajador que solicito la reforma
        $workers_area = Worker::with('user')->whereHas('departamento', function ($query) use ($area_id) {
            $query->where('area_id', $area_id);
        })->get();

        //trabajadores del mismo area  que tienen como rol analista(usuario con permisos para reformas)
        $analistas = $workers_area->filter(function ($value, $key) {
            return $value->user->hasRole('analista') == true;
        })->values();

        $correos_analistas = [];
        foreach ($analistas as $ana) {
            $correos_analistas[] = $ana->email;
        }

        $pac_destino = PacDestino::with('pac')->where('reforma_id', $reforma->id)->get();
        $correos_pac_destino = [];
        foreach ($pac_destino as $cpd) {
            $correos_pac_destino[] = $cpd->pac->worker->email;
        }

        $pac_origen = PacOrigen::with('pac')->where('reforma_id', $reforma->id)->get();
        $correos_pac_origen = [];
        foreach ($pac_origen as $cpo) {
            $correos_pac_origen[] = $cpo->pac->worker->email;
        }

        //arreglo con la union de los arreglos de correos eliminando los repetidos
        $para = array_unique(array_merge($correos_analistas, $correos_pac_origen, $correos_pac_destino));

        //email del usuario que solicita la reforma
        $user_sol = $reforma->user->email;

        $users_to = '';
        if ($accion == 'eliminada') {//cancelada: solo al solicitante de reforma
            $users_to = $user_sol; //usuario que creo la reforma

        }
        if ($accion == 'aprobada') {//aprobada a los que tienen rol analista y compradores dueño del proceso todos los implicados, pac origen y destino
            $users_to = $para;

        }

        Mail::send('emails.new_status_reforma', ['accion' => $accion, 'reforma' => $reforma], function ($message) use ($users_to, $accion) {
            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Reforma ' . $accion);
            $message->to($users_to);

        });

        if (Mail::failures()) {
            $message = 'Ocurrio un error al enviar el informe técnico';
            return response()->json(["message" => $message, "tipo" => 'error']);
//            return back()->with(['message_danger' => 'Ocurrio un error al enviar la notificación']);

        }
    }


    /**
     * Borrar solo de prueba para abrir directamente l infoeme tecnico de reforma
     * @param $id
     * @return mixed
     */
    public function verInformePDF($id)
    {
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        $reforma = Reforma::with('pac_origen', 'reform_type', 'area_item', 'user')->where('id', $id)->first();

        $area_id = $reforma->area_item->area_id;

        //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
        $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
            $query->where('area_id', $area_id)
                ->where('departamento', 'like', "%direcc%");
        })->first();

        $pac_destino = PacDestino::with('pac')->where('reforma_id', $reforma->id)->first();

        $pdf = PDF::loadView('reformas.informeT-pdf', compact('reforma', 'jefe_area', 'fecha_actual', 'month', 'pac_destino'));

        return $pdf->stream('Informe Tecnico');
    }

}
