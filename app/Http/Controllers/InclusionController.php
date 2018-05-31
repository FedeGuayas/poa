<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Detalle;
use App\InclusionPac;
use App\Item;
use App\Month;
use App\Pac;
use App\PacDestino;
use App\PacOrigen;
use App\Programa;
use App\Reforma;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Requests\InclusionPACStoreRequest;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class InclusionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**
     * Muestra vista para crear Inclusion Poa de FDG para item poa no programado en un mes determinado
     * @param Request $request
     * @return mixed
     */
    public function poaInclusion(Request $request)
    {

        $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
        $list_programs = $programas->pluck('programa', 'id');

        $areas = Area::all();
        $list_areas = $areas->pluck('area', 'id');

        $meses = Month::select(DB::raw('month,cod'))->get();
        $list_meses = $meses->pluck('month', 'cod');

        $mes = $request->input('mes');

        if ($request->ajax()) {
            $prog_id = $request->get('prog_id');
            $prog = Programa::where('id', $prog_id)->first();
            $actividad_list = $prog->actividads;

            return response()->json($actividad_list);
        }
        return view('inclusion.inclusion', compact('list_programs', 'list_areas', 'list_meses', 'mes'));

    }

    /**
     * cargar item al seleccionar la actividad, para la inclusion
     */
    public function getItem(Request $request)
    {

        if ($request->ajax()) {
            $prog_id = $request->input('prog_id');
            $act_id = $request->input('act_id');

            $act_prog_id = DB::table('actividad_programa')->where('actividad_id', $act_id)->where('programa_id', $prog_id)->first();

            //nuevo item =>presupuesto=0
//            $items = Item::where('actividad_programa_id', $act_prog_id->id)->where('presupuesto', '=', 0)->get();
            //todos los items
            $items = Item::where('actividad_programa_id', $act_prog_id->id)->get();

            return response()->json($items);
        }
    }

    /**
     * Codigo unico de programa-actividad-item
     * @param Request $request
     * @return mixed
     */
    public function getUniqueItem(Request $request)
    {
        $item_id = $request->input('item_id');
        $item = Item::with('extras', 'areas')->where('id', $item_id)->first();

        if ($request->ajax()) {
            return response()->json($item);
        }
    }

    /**
     *  Cargar detalles de las inclusiones DEL POA repartido para esa area
     * @param Request $request
     * @return mixed
     */
    public function loadItemArea(Request $request)
    {
        $area_id = $request->input('area_id');
        $item_id = $request->input('item_id');
        $area_item = DB::table('area_item as ai')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->select('ai.id', 'ai.item_id', 'ai.monto', 'ai.mes', 'ai.area_id', 'ai.item_id', 'm.month')
            ->where('area_id', $area_id)
            ->where('item_id', $item_id)
            ->get();

        return view('inclusion.listInclusion', compact('area_item'));
    }

    /**
     * Guardar inclusion poa del area en la tabla area_item como inclusion con monto $0
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storeInclusion(Request $request)
    {
        try {
            DB::beginTransaction();
            $item = Item::where('id', $request->input('item'))->first();
            $areas = $request->input('area_id');
            $meses = $request->input('mes');
            $montos = $request->input('subtotal_id');

            $cont = 0;
            while ($cont < count($areas)) {
                $item->areas()->attach($areas[$cont], ['monto' => $montos[$cont], 'mes' => $meses[$cont], 'inclusion' => AreaItem::INCLUSION_YES]);
                $cont++;
            }

            DB::commit();
            return redirect()->route('admin.inclusion')->with('message', 'Inclusión POA Guardada');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.inclusion')->with('message_danger', 'Error no se guardaron los registros en la BBDD');
//                return redirect()->route('admin.inclusion')->with('message_danger',$e->getMessage());
        }

    }

    /**
     * Eliminar la inclusión POA
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $poafdg = AreaItem::findOrFail($id);

            $mes = Month::where('cod', $poafdg->mes)->first();

            $reforma=Reforma::where('area_item_id',$poafdg->id)->first();

            //si el poa no es inclusion o esta en alguna reforma, no eliminar
            if ($poafdg->inclusion===AreaItem::INCLUSION_NO || $reforma){
                $message = 'La inclusión no puede ser eliminada porque o no es una inclusión, o se encuentra asociado a una reforma';
                return response()->json(['message' => $message,'tipo'=>'error']);
            }

            $poafdg->delete();
            $message = 'Inclusion del mes ' . $mes->month . ' eliminada';

            DB::Commit();

            return response()->json(['message' => $message]);

        } catch
        (\Exception $e) {
            DB::Rollback();
            $message = 'Ocurrio un error al intentar eliminar la inclusión';
//            $message = $e->getMessage();
            return response()->json(['message' => $message, 'tipo' => 'error']);
        }


    }

    /**
     * Vista para crear la planificacion de las inclusiones de los procesos pac o no pac /Procesos/Inclusion
     * Muestar todos los poas de las areas, los de item nuevos en rojo y los existentes en verde
     *
     * @return \Illuminate\Http\Response
     */
    public function indexIncPac(Request $request)
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
                ->select('ai.id', 'i.cod_item','ai.inclusion', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'm.month as mes', 'a.area', DB::raw('sum(p.presupuesto) as distribuido'))
                ->groupBy('ai.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'mes', 'a.area')
                ->where('area_id', 'like', '%' . $area_select . '%')
                ->get();

            $inclusiones = DB::table('pacs as p')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('area_item as ai', 'ai.id', '=', 'p.area_item_id')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->select('p.id', 'p.cod_item','p.item', 'm.month as mes', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento','p.tipo_compra', 'p.concepto', 'i.cod_programa', 'i.cod_actividad','p.presupuesto')
                ->where('ai.area_id', $area_select)
                ->where('p.inclusion', Pac::PROCESO_INCLUSION_SI)
                ->get();

            return view('inclusion.planificacion-inc-pac', compact('list_areas', 'area_item', 'area_select', 'inclusiones', 'area'));
        } else return abort(403);
    }


    /**
     * Vista para crear inlcusión del proceso del area
     *
     * @return \Illuminate\Http\Response
     */
    public function createPacInclusion(Request $request, $id)
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

            return view('inclusion.createPacInclusion', compact('area_item', 'list_workers', 'area', 'codigos'));
        } else return abort(403);
    }


    /**
     * Guardar el proceso pac de la inclusion, una vez guardado
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storePacInclusion(InclusionPACStoreRequest $request)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $area_item = AreaItem::where('id', $request->area_item_id)->first();
            $worker = Worker::where('id', $request->worker)->first();
            $valor = 0;
            $proceso_pac = $request->input('proceso_pac');

            $presupuesto = $valor;

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
            $pac->inclusion = Pac::PROCESO_INCLUSION_SI;
            $pac->presupuesto = $presupuesto;
            $pac->area_item()->associate($area_item);
            $pac->worker()->associate($worker);
            $area_item->inclusion = AreaItem::INCLUSION_NO;
            $pac->save();
            $area_item->update();

            return redirect()->route('indexIncPac')->with('message', 'Inclusion de proceso asignada correctamente');
        } else return abort(403);
    }

    /**
     * Editar la inclusion del proceso
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */

    public function incPacEdit(Request $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            $pac=Pac::where('id',$id)->with('area_item','worker')->first();
//            dd($pac);

            $codigos = DB::table('area_item as ai')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->select('i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item')
                ->where('ai.id', $pac->area_item_id)
                ->first();

            $workers = Worker::select(DB::raw('concat (nombres," ",apellidos) as nombre,id'))->get();
            $list_workers = $workers->pluck('nombre', 'id');

            return view('inclusion.editPacInclusion', compact( 'list_workers','codigos','pac'));
        } else return abort(403);

    }

    /**
     * Actualizar inclusion
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function incPacUpdate(Request $request, $id)
    {

        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            try {

                DB::beginTransaction();

                $pac = Pac::where('id',$id)->first();

                $worker = Worker::where('id', $request->input('worker'))->first();

                $proceso_pac = $request->input('proceso_pac');

                $pac->concepto = strtoupper($request->input('concepto'));
                $pac->procedimiento = strtoupper($request->input('procedimiento'));
                $pac->tipo_compra = strtoupper($request->input('tipo_compra'));
                $pac->cpc = strtoupper($request->input('cpc'));

                if ($proceso_pac == 'on') {
                    $pac->esProcesoPac();
                } else $pac->noEsProcesoPac();

                $pac->worker()->associate($worker);
                $pac->update();

                DB::commit();

                return redirect()->route('indexIncPac')->with('message', 'La inclusion se actualizo correctamente');

            }catch  (\Exception $e){
                DB::rollback();
                $message='Error al actualizar los registros';
//                return redirect()->route('indexIncPac')->with('message_danger', $message);
                return redirect()->route('indexIncPac')->with('message_danger', $e);
            }



        } else return abort(403);

    }

    /**
     * Eliminar inclusion recien creada que no este ya en reformas
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroyInclusionPac(Request $request, $id)
    {

        $user_login = $request->user();

        if ($user_login->can('planifica-pac')) {

            try {
                DB::beginTransaction();

                $pac = Pac::findOrFail($id);

                $reforma_origen=PacOrigen::where('pac_id',$pac->id)->first();
                $reforma_destino=PacDestino::where('pac_id',$pac->id)->first();
                $gestion=Detalle::where('pac_id',$pac->id)->first();

                //si el proceso no es inclusion o esta en alguna reforma o alguna gestion, no eliminar
                if ($pac->inclusion===Pac::PROCESO_INCLUSION_NO || $reforma_origen || $reforma_destino || $gestion){
                    $message = 'El proceso no puede ser eliminado porque o no es una inclusión, o se encuentra asociado a una reforma o gestion';
                    return response()->json(['message' => $message,'tipo'=>'error']);
                }

                $pac->delete();

                DB::Commit();

                $message = 'Inclusión eliminada';
                return response()->json(['message' => $message]);

            } catch
            (\Exception $e) {
                DB::Rollback();
                $message = 'Ocurrio un error al intentar eliminar la inclusión';
//            $message = $e->getMessage();
                return response()->json(['message' => $message, 'tipo' => 'error']);
            }

        } else return abort(403);
    }


    /**
     * VIsta para crear Solicitud de inclusion de procesos pac o no pac
     *
     * @return \Illuminate\Http\Response
     */
    public function createIPAC(Request $request)
    {
        $data = $request->all();
        $pac_id = key($data);
        $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        return view('inclusion.createIPac', compact('pac', 'month', 'fecha_actual'));
    }

    /**
     * Generar PDF de Inclusion Pac y registro en la tabla de inclusion_pac
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storeIPAC(Request $request)
    {
        $rules = [
            'cod_item' => 'required',
            'cpc' => 'required',
            'tipo_compra' => 'required',
            'concepto' => 'required',
            'presupuesto' => 'required|numeric',
            'pac_id' => 'required',
        ];

        $messages = [
            'cod_item.required' => 'La partida presupuestaria es requerida',
            'cpc.required' => 'El CPC es requerido',
            'tipo_compra.required' => 'El tipo de compra es requerido',
            'concepto.required' => 'El detalle del producto es requerido',
            'presupuesto.required' => 'El valor del PAC INICIAL es requerido',
            'presupuesto.numeric' => 'El valor del PAC INICIAL debe ser un número',
            'pac_id.required' => 'El pac es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cod_item = $request->input('cod_item');
        $cpc = $request->input('cpc');
        $tipo_compra = $request->input('tipo_compra');
        $concepto = strtoupper($request->input('concepto'));
        $presupuesto = $request->input('presupuesto');
        $pac_id = $request->input('pac_id');

        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        try {
            DB::beginTransaction();

            $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

            if (!isset($pac)) {
                return back()->withInput()->with('message_danger', 'No se encontró el PAC');
            }

            $area_id = $pac->area_item->area_id;
            //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
            $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                $query->where('area_id', $area_id)
                    ->where('departamento', 'like', "%direcc%");
            })->first();

            if (!isset($jefe_area)) {
                return back()->withInput()->with('message_danger', 'No existe un jefe de área definido');
            }

            $inclusionpac = new InclusionPac();
            $inclusionpac->pac()->associate($pac);
            $inclusionpac->cod_item = $cod_item;
            $inclusionpac->cpc = $cpc;
            $inclusionpac->tipo_compra = $tipo_compra;
            $inclusionpac->concepto = $concepto;
            $inclusionpac->presupuesto = $presupuesto;
            $inclusionpac->user_sol_id = $request->user()->id;
            $inclusionpac->save();

            $pdf = PDF::loadView('inclusion.inc-pac-pdf', compact('pac', 'month', 'fecha_actual', 'inclusionpac', 'jefe_area'))->setPaper('A4', 'portrait');

            DB::commit();

            return $pdf->setPaper('A4', 'portrait')->download('Inclusion-PAC' . $inclusionpac->id . '.pdf');

        } catch (\Exception $exception) {
            DB::rollback();
            return back()->with('message_danger', $exception->getMessage());
        }
    }

    /**
     * Subir el pdf de la Inclusion pac aprobado y adjuntar archivo en correo a comprador
     * @param Request $request
     * @return mixed
     */
    public function postFileIncPAC(Request $request)
    {
        $rules = [
            'incpac-file' => 'bail|required|mimes:pdf|max:100',
        ];

        $messages = [
            'required' => 'No selecciono ninguna archivo',
            'max' => 'El pdf no puede superar los 100Kb, pruebe con uno mas pequeño',
            'mimes' => ' El archivo debe ser un pdf',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors'=>$validator->messages()], 422);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $user_login = $request->user();

        try {

            DB::beginTransaction();

            //pac al que se hizo la inclsucion pac
            $pac = Pac::with('worker')->where('id', $request->input('inclusion_pac_id'))->first();

            //obtengo la ultima solicitud inclusion pac actual proceso de la tabla
            $incpac = InclusionPac::where('pac_id', $pac->id)->get()->last();

            if ($request->hasFile('incpac-file')) {

                //almaceno el nuevo archivo
                $file = $request->file('incpac-file');
                $name = 'IncPac' . '' . $incpac->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/pac/inclusion';
                $file->move($path, $name);
                $incpac->inclusion_file = $name;
            }
            $incpac->user_aprueba_id = $user_login->id; //usuario que subio el archivo
            $incpac->status = InclusionPac::INCLUSION_PAC_ACTIVA; //bandera para identificar que hay un archivo activo, desactivar despues de reforma
            $incpac->update();

            $this->sendIncPacUploadFileMail($pac, $incpac);

            DB::commit();
            $message='Se subió el archivo de Inclusión PAC correctamente y se notificó por correo al responsable del proceso.';
            if ($request->ajax()) {
                return response()->json(['message'=>$message], 200);
            } else {
                return back()->with(['message' => $message]);
            }

        } catch (\Exception $exception) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json(['message_error'=>'Error crítico: '.$exception->getMessage().'']);
            } else {
                return back()->with('message_danger', $exception->getMessage());
            }
        }
    }


    /**
     * Descargar el archivo pdf de la Inclusion PAC
     * @param $id
     * @return mixed
     */
    public function IncPacDownload(Request $request, $pac_id)
    {
        $pac = Pac::where('id', $pac_id)->first();

        if ($pac) { //existe el pac

            $incpac = InclusionPac::where('pac_id', $pac->id)->get()->last();

            if (isset($incpac) && isset($incpac->inclusion_file)) //existe y no es null el archivo de la inclusion
            {
                $pathtoFile = public_path() . '/uploads/pac/inclusion/' . $incpac->inclusion_file;
                return response()->download($pathtoFile);

            } else {
                return back()->with(['message_danger' => 'NO existe la Inclusión PAC para el pac seleccionado. O no se encuentra el archivo']);
            }
        } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

    }


    /**
     * Correo de notificacion de subida de archivo de Inclusion Pac a responsable del pac
     * @param $user
     * @param $pass
     */
    public function sendIncPacUploadFileMail($pac, $incpac)
    {
        $user_to = $pac->worker->email; //responsable del preoceso
        $path = public_path() . '/uploads/pac/inclusion/' . $incpac->inclusion_file;
        $name_file = $incpac->inclusion_file;

        Mail::send('emails.new_incpac_upload', ['pac' => $pac, 'incpac' => $incpac], function ($message) use ($user_to, $path, $name_file) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Solicitud de Inclusión PAC aprobada');
            $message->to($user_to);
            $message->attach($path, ['as' => $name_file, 'mime' => 'application/pdf']);

        });

        if (Mail::failures()) {
            $message='Ocurrio un error al enviar la notificación';
            return back()->with(['message_danger' => $message]);
        }
    }


}
