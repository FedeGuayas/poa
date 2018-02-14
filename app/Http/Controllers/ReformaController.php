<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Pac;
use App\PacDestino;
use App\PacOrigen;
use App\Programa;
use App\Reforma;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Illuminate\Support\Facades\Auth;
use function Sodium\add;

class ReformaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('imprimir-reformas')) {

            $reformas = DB::table('reformas as r')
                ->join('users as u', 'u.id', '=', 'r.user_id')
                ->join('workers as w', 'w.id', '=', 'u.worker_id')
                ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
                ->join('months as m', 'm.cod', '=', 'ai.mes')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('items as i', 'i.id', '=', 'ai.item_id')
                ->join('pac_destino as pd', 'pd.reforma_id', '=', 'r.id')
                ->select('r.id', 'r.estado', 'r.monto_orig','r.tipo', 'w.nombres', 'w.apellidos', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto', 'a.area'
                    ,DB::raw('sum(pd.valor_dest) as total_destino')
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
    public function createReforma(Request $request, $id)
    {
        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
//        $month = $fecha_actual->formatLocalized('%B');//ENERO, FEBRERO, etc
        $month = $fecha_actual->month;//1,2,...

        $pac = Pac::findOrFail($id);

        $poa = AreaItem::where('id', $pac->area_item_id)->first();


        //codigos del item y el id del area_item(POA-FDG)
        $codigos = DB::table('area_item as ai')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.id')
            ->where('ai.id', $poa->id)
            ->first();

        //procesos con saldo disponible en este poa para tener en cuenta en la reforma para los pac de origen
        $pacs = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'i.cod_programa', 'i.cod_actividad', 'p.area_item_id', 'a.area')
            ->where('p.area_item_id', $poa->id)
            ->where('p.disponible', '>', 0)
            ->get();

        //valor maximo disponible para tomar en la reforma
        $poa_disponible = Pac::where('area_item_id', $poa->id)->where('disponible', '>', 0)->sum('disponible');

        return view('reformas.createReforma', compact('pacs', 'poa_disponible', 'codigos', 'month', 'valor_mes'));


//        $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
//        $list_programs = $programas->pluck('programa', 'id');
//        $areas = Area::all();
//        $list_areas = $areas->pluck('area', 'id');
//        $area_select=$request->input('area');
//        $area_item = DB::table('area_item as ai')
//            ->join('items as i', 'ai.item_id','=','i.id')
//            ->join('areas as a','a.id','=','ai.area_id')
//            ->select('ai.id','i.cod_item','i.cod_programa','i.cod_actividad','i.item','ai.monto','ai.mes','a.area')
//            ->where('area_id','like','%'.$area_select.'%')
//            ->get();

//        $pacs=Pac::with('worker')

//            ->where('area_item_id',$poa->id)
//            ->where('disponible','>',0)
//            ->get();


//        $poa = Pac::has('disponible', '>', 0)->get();
        // Todos loas pacs del poa con disponibilidad > 0
//        $poa = AreaItem::whereHas('pacs', function ($query) use ($pac) {
//            $query->where('disponible', '>', 0);
//        })->get();


//        return view('reformas.createReforma',compact('pacs','poa_disponible','codigos','list_programs','list_areas','area_select','area_item','pacs_All'));

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
            $disponible = $request->input('disponible');//max valor que se puede tomar de este poa

            //el monto para la reforma no puede ser mayor que lo que tiene el item sin ejecutar          
            if ($total > $disponible) {
                $message = 'No puede tomar un valor mayor a lo disponible';
//                return response()->json(["message" => $message, "tipo" => 'error']);
                return redirect()->back()->withInput()->with('message_danger', $message);
            } else $monto_orig = $total;

            //el valor total de las reformas que estan pendiente sobre este item
            $reformado_pendiente = Reforma::where('area_item_id', $area_item_id)->where('estado', 'Pendiente')->sum('monto_orig');
            //lo que se puede reformar segun lo que hay pendiente de aprobacion
            $por_reformar = $disponible - $reformado_pendiente;

            if ($total > $por_reformar) {
                $message = 'Existen Reformas pendientes sobre este poa, solo hay disponible $ ' . $por_reformar . ' dolares';
//                return response()->json(["message" => $message, "tipo" => 'error']);
                return redirect()->back()->withInput()->with('message_danger', $message);
            }

            if (($request->input('tipo')=='INTERNA') &&  count($pac_id)>1) {
                $message = 'Para la reforma interna debe seleccionar solo un origen.';
//                return response()->json(["message" => $message, "tipo" => 'error']);
                return redirect()->back()->withInput()->with('message_danger', $message);
            }

            $reforma = new Reforma();
            $reforma->area_item()->associate($poafdg);
            $reforma->user_id = $user_login->id;
            $reforma->monto_orig = $monto_orig;
            $reforma->estado = 'Pendiente';
            $reforma->tipo = $request->input('tipo');
            $reforma->nota = strtoupper($request->input('nota'));
            $reforma->save();

            //guardar los  pac origen
            $cont = 0;
            while ($cont < count($pac_id)) {

                $pac = Pac::where('id', $pac_id[$cont])->first(); //pac origen

                //comprobar la disponibilidad del dinero del pac comprobando si hay reformas sobre este pac pendientes
                $pac_pendiente = PacOrigen::where('pac_id', $pac_id[$cont])->where('estado', 'Pendiente')->sum('valor_orig');
                $pendiente = $pac->disponible - $pac_pendiente;
                if ($valor_orig[$cont] > $pendiente) {
                    $message = 'Existen reformas pendientes. El item ' . $pac->cod_item . ' por concepto ' . $pac->concepto . ', solo tiene disponible $ ' . $pendiente . ' dolares';
//                    return response()->json(["message" => $message, "tipo" => 'error']);
                    return redirect()->back()->withInput()->with('message_danger', $message);
                }

                //guardo el pac de origen, estado por defecto Pendiente
                $pac_origen = new PacOrigen();
                $pac_origen->reforma()->associate($reforma);
                $pac_origen->pac_id = $pac_id[$cont];
                $pac_origen->valor_orig = $valor_orig[$cont];
                $pac_origen->save();
                $cont++;
            }

            DB::commit();
            //devolver valores necesarios para reforma destino
            return redirect()->route('destinoReforma',["reforma_id" => $reforma->id, "pacs_origen_id" => $pac_id]);
            //return response()->json(["monto_reforma" => $reforma->monto_orig, "reforma_id" => $reforma->id, "pacs_origen_id" => $pac_id]);
        } catch (\Exception $e) {
            DB::rollback();
//            return response()->json(["response" => $e->getMessage(), "tipo" => "error"]);
            $message=$e->getMessage();
            $message="Ha ocurrido un error al guardar los registros en la base de datos";
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

        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
//        $month = $fecha_actual->formatLocalized('%B');//ENERO, FEBRERO, etc
        $month = $fecha_actual->month;//1,2,...

        $array_pacs_origen_id = $request->input('pacs_origen_id');

        $reforma= Reforma::where('id',$request->input("reforma_id"))->with('pac_origen')->first();

        $poa = AreaItem::where('id', $reforma->area_item_id)->first();

        if ($reforma->tipo == 'INTERNA'){
            //en la reforma interna solo hay un pac de origen y este lo tengo que excluir de los pacs a devolver
            $pac_origen_id= array_first($array_pacs_origen_id);

            $user_login=$request->user();

            $pacs_All = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'i.cod_programa', 'i.cod_actividad', 'p.area_item_id')
                ->where('p.id', '<>', $pac_origen_id)//no mostrar el pac del k estoy kitando dinero
                ->where('p.mes', '>=', $month)//no mostrar el pac de meses anteriores al presente
                ->where('p.area_item_id','=', $poa->id)//muestra los pac del poa de donde estoy kitando dinero
                ->where('p.worker_id','=', $user_login->id)//muestra los pac del poa de donde estoy kitando dinero
                ->get();
        }else{

            //pacs para el destino de la reforma que no sean interna
        $pacs_All = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'i.cod_programa', 'i.cod_actividad', 'p.area_item_id')
//            ->where('p.id', '<>', $id)//no mostrar el pac del k estoy kitando dinero
            ->where('p.mes', '>=', $month)//no mostrar el pac de meses anteriores al presente
            ->where('p.area_item_id','<>',$poa->id)//no muestra los pac del poa de donde estoy kitando dinero
            ->get();

        }

        //valor maximo disponible para tomar en la reforma
        $poa_disponible = Pac::where('area_item_id', $poa->id)->where('disponible', '>', 0)->sum('disponible');

        return view('reformas.destino', compact( 'poa_disponible','reforma', 'pacs_All', 'month'));

    }


    /**
     * Guaradar los pacs de Detsino de la reforma y redirigir a la pagina de listar reformas
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storePacsDestino(Request $request)
    {
        try {
            DB::beginTransaction();

            $monto_reforma = $request->input('monto_reforma');
            $por_distribuir = $request->input('por_distribuir');
            $total_destino = $request->input('total_destino');
            $pac_id = $request->input('pac_idd');//arreglo de los pac_id de destino
            $valor_dest = $request->input('subtotal_idd');//arreglo con los valores de pac de destino
            $reforma_id = $request->input('reforma_id');
            $reforma = Reforma::findOrFail($reforma_id);
            $tipo_reforma=$reforma->tipo;//tipo de reforma

            $poa_origen_id=$reforma->area_item_id;//id poa de origen de la reforma

            if ($por_distribuir > 0 || $monto_reforma != $total_destino) {
                $message = 'Debe distribuir todo el dinero de la reforma';
                return response()->json(["message" => $message, "tipo" => 'error']);
            }

            if ($tipo_reforma == 'INTERNA') {
                $cont = 0;
                $pac_control = 0;
                while ($cont < count($pac_id)) {

                    if ($pac_control == $pac_id[$cont]) {
                        $message = 'Optimice su reforma, ha sumado dos valores en un mismo item';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    $pac=Pac::where('id',$pac_id[$cont])->first();//pac destino
                    if ($pac->area_item_id != $poa_origen_id) {
                        $message = 'En la reforma interna solo se admiten como destino los items del mismo origen y mes';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    if (Auth::user()->id != $reforma->user_id || Auth::user()->worker_id != $pac->worker_id) {
                        $message = 'En la reforma interna solo se admiten items propios del usuario ';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

                    $pac_dest = new PacDestino();
                    $pac_dest->reforma()->associate($reforma);
                    $pac_dest->pac_id = $pac_id[$cont];
                    $pac_dest->valor_dest = $valor_dest[$cont];
                    $pac_dest->save();
                    $pac_control = $pac_id[$cont];
                    $cont++;
                }

                //*** Reforma interna no necesita aprobacion, actualixzar valores al mismo tiempo k se guarda ***//



                $poaorigen = AreaItem::where('id', $poa_origen_id)->first();//poa al que se le quitara saldo

                $monto_destino_total = PacDestino::where('reforma_id', $reforma->id)->sum('valor_dest'); //suma de todos los pac destino, puede ser mas de un poa

                //actualizar los montos de cada poafdg destino (area_item) y los pac
                foreach ($reforma->pac_destino as $pac_dest) {
                    $pac = Pac::where('id', $pac_dest->pac_id)->first();
                    $poa_dest = $pac->area_item; //objeto, relacion belongsTo
                    $poa_dest->monto = $poa_dest->monto + $pac_dest->valor_dest;
                    $poa_dest->update();

                    //sumar los valores al pac destino
                    $pac->presupuesto = $pac->presupuesto + $pac_dest->valor_dest;
                    $pac->disponible = $pac->disponible + $pac_dest->valor_dest;
                    $pac->update();
                }

                //actualizar el monto del poafdg origen  (area_item)
                $poaorigen = $reforma->area_item; // objeto, relacion belongsTo
                $poaorigen_actual = ($poaorigen->monto) - ($reforma->monto_orig);
                $poaorigen->monto = $poaorigen_actual;
                $poaorigen->update();

                //actualizar valores de pacs origen
                foreach ($reforma->pac_origen as $pac_orig) {
                    $pac = Pac::where('id', $pac_orig->pac_id)->first();
                    $pac->presupuesto = $pac->presupuesto - $pac_orig->valor_orig;
                    $pac->disponible = $pac->disponible - $pac_orig->valor_orig;
                    $pac->update();

                    $pac_orig->estado = 'Aprobada';
                    $pac_orig->update();
                }

                $reforma->estado = 'Aprobada';
                $reforma->update();

                if ($reforma->monto_orig != $monto_destino_total) {
                    $message = 'Los montos del origen ($' . $reforma->monto_orig . ') y destino ($' . $monto_destino_total . ') no coinciden.';
                    return response()->json(["message" => $message, "tipo" => 'error']);
                }


            } else { //Reforma INFORMATIVA o MINISTERIAL

                $cont = 0;
                $pac_control = 0;
                while ($cont < count($pac_id)) {

                    if ($pac_control == $pac_id[$cont]) {
                        $message = 'Optimice su reforma, ha sumado dos valores en un mismo item';
                        return response()->json(["message" => $message, "tipo" => 'error']);
                    }

//                $pac_pendiente=PacDestino::where('pac_id',$pac_id[$cont])->sum('valor_dest');
//                if ($pac_pendiente>0){
//                    $message='Existen reformas pendientes al item '.$pac->cod_item.' por concepto '.$pac->concepto;
//                    return response()->json(["message"=>$message,"tipo"=>'error']);
//                }

                    $pac_dest = new PacDestino();
                    $pac_dest->reforma()->associate($reforma);
                    $pac_dest->pac_id = $pac_id[$cont];
                    $pac_dest->valor_dest = $valor_dest[$cont];
                    $pac_dest->save();
                    $pac_control = $pac_id[$cont];
                    $cont++;
                }
            }

            DB::commit();
            return response()->json(["message" => "Reforma creada correctamente"]);
        } catch (\Exception $e) {
            DB::rollback();
            $reforma = Reforma::where('id', $request->input('reforma_id'))->first();
            $reforma->delete();
            //return response()->json([ "response" => $e->getMessage(),"tipo" => "error"]);
            return response()->json(["message" => "Lo sentimos, ha ocurrido un error interno, recargue la pagina y comience la reforma nuevamente", "tipo" => "error_critico"]);
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $reforma = Reforma::findOrFail($id);
        $reforma->delete();
        $message = 'Reforma eliminada';
        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
    }

    public function confirm(Request $request, $id)
    {
        $reforma = Reforma::findOrFail($id);

        $poaorigen = AreaItem::where('id', $reforma->area_item_id)->first();//poa al que se le quitara saldo

        $monto_destino_total = PacDestino::where('reforma_id', $reforma->id)->sum('valor_dest'); //suma de todos los pac destino, puede ser mas de un poa

        //actualizar los montos de cada poafdg destino (area_item) y los pac
        foreach ($reforma->pac_destino as $pac_dest) {
            $pac = Pac::where('id', $pac_dest->pac_id)->first();
            $poa_dest = $pac->area_item;
            $poa_dest->monto = $poa_dest->monto + $pac_dest->valor_dest;
            $poa_dest->update();

            //sumar los valores al pac destino
            $pac->presupuesto = $pac->presupuesto + $pac_dest->valor_dest;
            $pac->disponible = $pac->disponible + $pac_dest->valor_dest;
            $pac->update();
        }

        //actualizar el monto del poafdg origen  (area_item)
        $poaorigen = $reforma->area_item;
        $poaorigen_actual = ($poaorigen->monto) - ($reforma->monto_orig);
        $poaorigen->monto = $poaorigen_actual;
        $poaorigen->update();

        //actualizar valores de pacs origen
        foreach ($reforma->pac_origen as $pac_orig) {
            $pac = Pac::where('id', $pac_orig->pac_id)->first();
            $pac->presupuesto = $pac->presupuesto - $pac_orig->valor_orig;
            $pac->disponible = $pac->disponible - $pac_orig->valor_orig;
            $pac->update();

            $pac_orig->estado = 'Aprobada';
            $pac_orig->update();
        }

        $reforma->estado = 'Aprobada';
        $reforma->update();

        if ($reforma->monto_orig != $monto_destino_total) {
            $message = 'Los montos del origen ($' . $reforma->monto_orig . ') y destino ($' . $monto_destino_total . ') no coinciden.';
            return response()->json(["message" => $message, "tipo" => 'error']);
        }

        $message = "Reforma aprobada";
        if ($request->ajax()) {
            return response()->json(["message" => $message]);
        }
        return redirect()->route('admin.reformas.index');
    }

//    public function getMes($mes)
//    {
//        $valor_mes = 0;
//        switch ($mes) {
//            case'ENERO':
//                $valor_mes = 1;
//                break;
//            case'FEBRERO':
//                $valor_mes = 2;
//                break;
//            case'MARZO':
//                $valor_mes = 3;
//                break;
//            case'ABRIL':
//                $valor_mes = 4;
//                break;
//            case'MAYO':
//                $valor_mes = 5;
//                break;
//            case'JUNIO':
//                $valor_mes = 6;
//                break;
//            case'JULIO':
//                $valor_mes = 7;
//                break;
//            case'AGOSTO':
//                $valor_mes = 8;
//                break;
//            case'SEPTIEMBRE':
//                $valor_mes = 9;
//                break;
//            case'OCTUBRE':
//                $valor_mes = 10;
//                break;
//            case'NOVEMBRE':
//                $valor_mes = 11;
//                break;
//            case'DICIEMBRE':
//                $valor_mes = 12;
//                break;
//        }
//        return $valor_mes;
//    }
}
