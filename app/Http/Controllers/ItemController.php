<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Apertura;
use App\Area;
use App\Extra;
use App\Item;
use App\Programa;
use Illuminate\Http\Request;
use App\Http\Requests\ItemStoreRequest;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

use DB;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador']);
    }
    /**
     * Listado de los items y crear items
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
        $list_programs = $programas->pluck('programa', 'id');

        $actividades = Actividad::select(DB::raw('concat (cod_actividad," - ",actividad) as actividad,id'))->get();
        $list_actividades = $actividades->pluck('actividad', 'id');

        $item_list = Item::all();

        $view = view('items.index', compact('list_programs','list_actividades', 'item_list'));
        if ($request->ajax()) {
            $sections = $view->rendersections();//renderizar la vista
            return response()->json($sections['content']);//en la seccion content
        } else return $view;
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemStoreRequest $request)
    {
        if ($request->input('actividad')=='placeholder'){
            return redirect()->route('admin.items.index')->withInput()->with('message_danger','Seleccione la actividad');
        }
        try {
            DB::beginTransaction();

            $programa_id = $request->input('programa');
            $programa = Programa::findOrFail($programa_id);
            $actividad_id = $request->input('actividad');
            $actividad = Actividad::findOrFail($actividad_id);
            $cod_item = $request->input('codigo');
            $item = $request->input('item');
            $presupuesto=$request->input('presupuesto');
            $disponible=$presupuesto;
            $grupo_gasto = substr($cod_item, 0, 2);
            $act_prog_id = DB::table('actividad_programa')->where('actividad_id', $actividad_id)->where('programa_id', $programa_id)->first();

            $items = new Item();
            $items->cod_programa = $programa->cod_programa;
            $items->cod_actividad = $actividad->cod_actividad;
            $items->cod_item = $cod_item;
            $items->item = strtoupper($item);
            $items->grupo_gasto = $grupo_gasto;
            $items->presupuesto = $presupuesto;
            $items->disponible = $disponible;
            $items->actividad_programa()->associate($act_prog_id->id);
            $items->save();
            $message="Item creado";

            DB::commit();
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'success']);
            }
            else  return redirect()->route('admin.items.index')->with('message',$message);

        } catch (\Exception $e) {

            DB::rollback();
            $message="Error al insertar los datos en la BBDD";
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            }else{
                return redirect()->route('admin.items.index')->with('message_danger',$message)->withInput();
//                return redirect()->route('admin.departamentos.index')->with('message_danger',$e->getMessage())->withInput();
            }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
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
        $item = Item::findOrFail($id);
        $programa_id = $request->input('programa_edit');
        $programa = Programa::findOrFail($programa_id);
        $actividad_id = $request->input('actividad_edit');
        $actividad = Actividad::findOrFail($actividad_id);


        if ($request->input('presupuesto_edit') > $item->presupuesto){
            $dif=$request->input('presupuesto_edit') - $item->presupuesto;
            $disp=$item->disponible+$dif;
        }else if ($request->input('presupuesto_edit') < $item->presupuesto){
            $dif=$item->presupuesto - $request->input('presupuesto_edit');
            $disp=$item->disponible-$dif;
        }else $disp=$request->input('presupuesto_edit');

        $item->disponible=$disp;
        $item->cod_programa=$programa->cod_programa;
        $item->cod_actividad=$actividad->cod_actividad;
        $item->cod_item = $request->input('codigo_edit');
        $item->grupo_gasto = substr($request->input('codigo_edit'), 0, 2);
        $item->item = strtoupper($request->input('item_edit'));
        $item->presupuesto=$request->input('presupuesto_edit');

        $act_prog_id = DB::table('actividad_programa')->where('actividad_id', $actividad_id)->where('programa_id', $programa_id)->first();

        $item->actividad_programa()->associate($act_prog_id->id);
        $result = $item->update();
        if ($result) {
            $mensaje = "Item actualizado";
            if ($request->ajax()) {
                return response()->json(["mensaje" => $mensaje,"estado" => 'success']);
            } else {
                return redirect()->route('admin.items.index')->with('message',$mensaje);
            }
        } else {
            if ($request->ajax()) {
                return response()->json(["estado" => 'error']);
            } else {
                return redirect()->route('admin.items.index')->with('message_danger','Error al actualizar');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        $message = 'Item ' . $item->item . ' eliminado';
        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
    }

    /**
     * Vista para cargar presupuestos
     *
     * @return \Illuminate\Http\Response
     */
    public function loadPresupuesto()
    {
        if (Auth::user()->can('importa-presupuesto')) {
            $presupuesto = Item::first();
            if (count($presupuesto) > 0) {
                $cargar = false;
            } else $cargar = true;
            return view('items.load_presupuesto', compact('cargar'));
        }else return abort(403);
    }


    /**
     * Importar Presupuesto anual
     * @param Request $request
     * @return mixed
     */
    public function importPresupuesto(Request $request)
    {
        if (Auth::user()->can('importa-presupuesto')) {

            if ($request->hasFile('item_file')) {
                $file = $request->file('item_file');
                $path = $file->getRealPath();
                $data = Excel::load($path, function ($reader) {
                })->get();

                $insert = [];
                if (!empty($data) && $data->count()) {
                    try {
                        DB::beginTransaction();

                        foreach ($data as $key => $value) {
                            if ($value->programa != '') {
                                $insert[] = [
                                    "programa" => $value->cod_programa,
                                    "nombre_programa" => strtoupper($value->programa),
                                    "actividad" => $value->cod_actividad,
                                    "nombre_actividad" => strtoupper($value->actividad),
                                    "renglon" => $value->cod_item,
                                    "nombre_item" => strtoupper($value->item),
                                    "codificado" => str_replace(',', '.', $value->presupuesto),
                                ];
                            }
                        }
                        if (!empty($insert)) {
                            DB::table('carga_inicial')->insert($insert);
                        }

                        //Lllenar resto de las tablas si esta vacia la tabla programas
                        $pro = Programa::all();
                        if (count($pro) <= 0) {
                            //llenado tabla programas
                            $programas = DB::table('carga_inicial')->select('programa', 'nombre_programa')->distinct()->get();
                            $insertPrograma = [];
                            if (!empty($programas) && count($programas) > 0) {
                                try {
                                    DB::beginTransaction();
                                    foreach ($programas as $key => $value) {
                                        $insertPrograma[] = [
                                            "cod_programa" => $value->programa,
                                            "programa" => $value->nombre_programa
                                        ];
                                    }
                                    if (!empty($insertPrograma)) {
                                        DB::table('programas')->insert($insertPrograma);
                                    }
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    return response()->json([
                                        "response" => "Ah ocurrido un error al cargar la tabla programas",
                                        "tipo" => "error"
                                    ]);
                                }
                            }

                            //llenado tabla actividads
                            $actividades = DB::table('carga_inicial')->select('actividad', 'nombre_actividad')->distinct()->get();
                            $insertActividad = [];
                            if (!empty($actividades) && count($actividades) > 0) {
                                try {
                                    DB::beginTransaction();
                                    foreach ($actividades as $key => $value) {
                                        $insertActividad[] = [
                                            "cod_actividad" => $value->actividad,
                                            "actividad" => $value->nombre_actividad,
                                        ];
                                    }
                                    if (!empty($insertActividad)) {
                                        DB::table('actividads')->insert($insertActividad);
                                    }
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    return response()->json([
                                        "response" => "Ha ocurrido un error al cargar la tabla actividades",
                                        "tipo" => "error"
                                    ]);
                                }
                            }

                            //llenado tabla actividad_programa
                            $programas2 = Programa::all();
                            foreach ($programas2 as $programa) {
                                $actividades = Apertura::select('actividad')->distinct()->where('programa', $programa->cod_programa)->get()->toArray();
                                $actividadesArray = array_flatten($actividades);//array de cod_actividads
                                $filtered = DB::table('actividads')->whereIn('cod_actividad', $actividadesArray)->get();
                                //dd($filtered);
                                foreach ($filtered as $key => $value) {
                                    $programa->actividads()->attach($value->id, ['cod_actividad' => $value->cod_actividad, 'cod_programa' => $programa->cod_programa]);
                                }
                            }

                            //llenado tabla items
                            $dataItem = DB::table('carga_inicial as ci')
                                ->join('actividad_programa as ap', function ($join) {
                                    $join->on('ci.programa', '=', 'ap.cod_programa')
                                        ->on('ci.actividad', '=', 'ap.cod_actividad');
                                })
                                ->select('ap.id', 'ap.cod_programa', 'ap.cod_actividad', 'ci.renglon', 'ci.nombre_item', 'ci.codificado')
                                ->get();
                            $insertItem = [];
                            if (!empty($dataItem) && count($dataItem) > 0) {
                                try {
                                    DB::beginTransaction();
                                    foreach ($dataItem as $key => $value) {
                                        $insertItem[] = [
                                            "actividad_programa_id" => $value->id,
                                            "cod_programa" => $value->cod_programa,
                                            "cod_actividad" => $value->cod_actividad,
                                            "cod_item" => $value->renglon,
                                            "item" => $value->nombre_item,
                                            "presupuesto" => str_replace(',', '.', $value->codificado),
                                            "disponible" => str_replace(',', '.', $value->codificado),
                                            "grupo_gasto" => substr($value->renglon, 0, 2)
                                        ];
                                    }
                                    if (!empty($insertItem)) {
                                        DB::table('items')->insert($insertItem);
                                    }
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    return response()->json([
                                        "response" => "Ah ocurrido un error al cargar la tabla items",
                                        "tipo" => "error"
                                    ]);
                                }
                            }
                        } //fin llenado tablas programas, actividads, actividad_programa, item. Solo en la primera carga

                        DB::commit();
                        return response()->json([
                            "response" => "Registros cargados"
                        ]);
                    } catch (\Exception $e) {
                        DB::rollback();
//                        Session::flash('message_danger', 'Error' . $e->getMessage());
                        return response()->json([
                            "response" => "Ah ocurrido un error al cargar el archivo",
//                       "response" => $e->getMessage(),
                            "tipo" => "error"
                        ]);
                    }
                }
            } else return response()->json(["response" => "Archivo incorrecto", "tipo" => "error"]);
        } else return abort(403);
    }

}
