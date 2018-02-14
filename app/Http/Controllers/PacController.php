<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Cpac;
use App\Detalle;
use App\Pac;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;

use App\Http\Requests\PacStoreRequest;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class PacController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador'], ['except' => ['index']]);
    }

    /**
     * Vista para crear la planificacion
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
                ->select('ai.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'm.month as mes', 'a.area', DB::raw('sum(p.presupuesto) as distribuido'))
                ->groupBy('ai.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'mes', 'a.area')
                ->where('area_id', 'like', '%' . $area_select . '%')
                ->get();

            $pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.comprometido', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'i.cod_programa', 'i.cod_actividad')
                ->where('a.id', $area_select)
                ->get();

            return view('pac.planificacion', compact('list_areas', 'area_item', 'area_select', 'pacs', 'area'));
        } else return abort(403);
    }

    /**
     * Lista El PAC del Area
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $fecha_actual = Carbon::now();
            $mes_actual = $fecha_actual->month;

            $areas = Area::all();
            $list_areas = $areas->pluck('area', 'id');
            $area_select = $request->input('area');

            $area = Area::where('id', $area_select)->first();

            $pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->join('departamentos as d', 'd.id', '=', 'w.departamento_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'm.cod', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'w.id as trabajador_id', 'a.area', 'p.procedimiento', 'p.concepto', 'p.comprometido', 'i.cod_programa', 'i.cod_actividad', 'd.area_id as area_trabajador', 'd.departamento', 'ai.area_id as aiID')
//            ->where('a.id',$area_select)
                ->where('ai.area_id', 'like', '%' . $area_select . '%')
                ->get();

            $pacs = collect($pacs);//convierto a colleccion para poder utilizar map()

            //recorro cada elemento de la coleccion para agregar un nuevo elemento donde indico si tiene un CPAC.pdf
            $pacs->map(function ($pac) {
                $cpac = Cpac::where('pac_id', $pac->id)->select('certificado')->get()->last(); //ultimo pdf de CPAC subido
                $cert = null;
                if ($cpac) { //si tiene un pdf asignado lo agrego
                    $cert = $cpac->certificado;
                }
                $pac->certificado_file = $cert;
                return $pac;
            });

            return view('pac.index', compact('list_areas', 'pacs', 'area_select', 'area', 'mes_actual'));
        } else return abort(403);
    }

    /**
     * Vista para crear PAC del area
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

//        $workers = Worker::
//        join('departamentos as d', 'd.id', '=', 'w.departamento_id', 'as w')
//            ->join('areas as a', 'a.id', '=', 'd.area_id')
//            ->join('area_item as ai', 'ai.area_id', '=', 'a.id')
//            ->select(DB::raw('concat (w.nombres," ",w.apellidos) as trabajador,w.id as id'))
//            ->where('ai.id', $id)
//            ->get();

            $workers = Worker::select(DB::raw('concat (nombres," ",apellidos) as nombre,id'))->get();
            $list_workers = $workers->pluck('nombre', 'id');

            $area = Area::
            join('area_item as ai', 'ai.area_id', '=', 'a.id', 'as a')
                ->select('a.area')
                ->where('ai.id', $id)
                ->first();

            //valor asignado a este item en la planificacion del pac
            $pac_presupuesto = $area_item->pacs()->where('mes', $area_item->mes)->sum('presupuesto');
            //$pacs2=AreaItem::has('pacs')->get();

            //maximo que se puede distribuir al responsable de este pac
            $total_disponible = $area_item->monto - $pac_presupuesto;

            //pac
            $pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'p.presupuesto', 'p.disponible', 'p.devengado', 'w.nombres', 'w.apellidos', 'p.procedimiento', 'p.concepto')
                ->where('p.area_item_id', $id)
                ->get();

            return view('pac.createPac', compact('area_item', 'list_workers', 'area', 'total_disponible', 'codigos', 'pacs'));
        } else return abort(403);
    }

    /**
     * Store a newly created resource in storage.
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
            $pac->presupuesto = $presupuesto;
            $pac->area_item()->associate($area_item);
            $pac->worker()->associate($worker);
            $pac->save();

            return redirect()->route('indexPlanificacion')->with('message', 'PAC asignado correctamente');
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

            $workers = Worker::
            join('departamentos as d', 'd.id', '=', 'w.departamento_id', 'as w')
                ->join('areas as a', 'a.id', '=', 'd.area_id')
                ->join('area_item as ai', 'ai.area_id', '=', 'a.id')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->select(DB::raw('concat (w.nombres," ",w.apellidos) as trabajador,w.id as id'))
                ->where('a.id', $pac->area_item->area_id)
                ->get();

            $list_workers = $workers->pluck('trabajador', 'id');

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

            $presupuesto_nuevo = $request->input('presupuesto');
            $total_disponible = $request->input('total_disponible');


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
            $pac->worker()->associate($worker);
            $pac->update();
            return redirect()->route('indexPlanificacion')->with('message', 'PAC actualizado correctamente');

        } else return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $pac = Pac::findOrFail($id);

        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $gestiones = $pac->detalles;

            if (count($gestiones) > 0) {
                $message = 'No se puede eliminar porque existen gestiones en el pac seleccionado';
                return response()->json(['message' => $message]);
            } else {
                $pac->delete();
                $message = 'Pac eliminado';
                if ($request->ajax()) {
                    return response()->json(['message' => $message]);
                }
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
     * Generar PDF de certificacion de pac y registro en la tabla de certificacion
     * @param $pac_id
     */
    public function certificacionPDF($pac_id)
    {
        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        try {
            DB::beginTransaction();

            $pac = Pac::where('id', $pac_id)
                ->first();

            if ($pac) { //si existe el pac, crear la certificacion
                $cpac = new Cpac();
                $cpac->pac()->associate($pac);
                $cpac->partida = $pac->cod_item;
                $cpac->cpc = $pac->cpc;
                $cpac->monto = floor(($pac->presupuesto / 1.12) * 100) / 100;
                $cpac->save();

            }
            DB::commit();

            $pdf = PDF::loadView('pac.cpac-pdf', compact('cpac', 'month', 'fecha_actual'))->setPaper('A4', 'portrait');
            return $pdf->setPaper('A4', 'portrait')->stream('CPAC' . $cpac->id . '.pdf');//imprime en pantalla
        } catch (\Exception $exception) {
            DB::rollback();
        }
    }

    /**
     * Actualizar el pdf de la CPAC
     * @param Request $request
     * @return mixed
     */
    public function postFileCPAC(Request $request)
    {
        $messages = [
            'required' => 'No selecciono ninguna archivo',
            'max' => 'El pdf no puede superar los 100Kb, pruebe con uno mas pequeño',
            'mimes' => ' El archivo debe ser un pdf',
        ];

        $rules = [
            'cpac-file' => 'bail|required|mimes:pdf|max:100',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator);
//            $errors = $validator->errors();
//            return back()->withDanger($errors->first('cpac-file'));
        }

        $pac = Pac::where('id', $request->input('cpac_pac_id'))->first();

        if ($pac) { //existe el pac

            $cpac = Cpac::where('pac_id', $pac->id)->get()->last();

            if (isset($cpac)) //existe y no es null la certificacion del pac CPAC
            {
                if ($request->hasFile('cpac-file')) {

                    //elimino archivo anterior
//                    $old_filename = public_path() . '/uploads/pac/certifications/' . $cpac->certificacion;
//                    \File::delete($old_filename);

                    //almaceno el nuevo archivo
                    $file = $request->file('cpac-file');
                    $name = 'CPAC' . '' . $cpac->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                    $path = public_path() . '/uploads/pac/certifications';
                    $file->move($path, $name);
                    $cpac->certificado = $name;
                }
                $cpac->update();
                return back()->with(['message' => 'Se subió el archivo de CPAC correctamente']);

            } else {
                return back()->with(['message_danger' => 'NO existe certificaión para el pac seleccionado. No es posible subir el arhivo']);
            }
        } else
            return back()->with(['message_danger' => 'Error!, PAC no encontrado']);
    }

    /**
     * Descargar el archivo pdf de la CPAC
     * @param $id
     * @return mixed
     */
    public function CPACDownload(Request $request, $pac_id)
    {
        $pac = Pac::where('id', $pac_id)->first();

        if ($pac) { //existe el pac

            $cpac = Cpac::where('pac_id', $pac->id)->get()->last();

            if (isset($cpac) && isset($cpac->certificado)) //existe y no es null la certificacion del pac CPAC
            {
                $pathtoFile = public_path() . '/uploads/pac/certifications/' . $cpac->certificado;
                return response()->download($pathtoFile);

            } else {
                return back()->with(['message_danger' => 'NO existe certificaión para el pac seleccionado. O no se encuentra la CPAC']);
            }
        } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

    }

}
