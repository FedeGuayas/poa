<?php

namespace App\Http\Controllers;


use App\Actividad;
use App\Apertura;
use App\Configuration;
use App\Programa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ImportPoaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Yajra\Datatables\Datatables;

class AperturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar POA cargado
     */
    public function index(Request $request)
    {

//        if ($request->ajax()){
        if (Auth::user()->can('importa-esigef')) {
            $poa = Apertura::select('ejercicio', 'cod_entidad', 'programa', 'actividad', 'renglon', 'nomb_entidad', 'nomb_geo', 'asignado', 'codificado', 'reserv_neg', 'precompromiso', 'compromiso', 'devengado', 'pagado', 'disponible', 'no_proyecto');

            return Datatables::of($poa)
                ->make(true);
        } else abort(403);
//        } else

//else  return response()->json($poa->get());
    }

    /**
     * Mostra la tabla con el POA cargado
     * @param Request $request
     * @return mixed
     */
    public function listPOA(Request $request)
    {
        if (Auth::user()->can('importa-esigef')) {
            return view('configuracion.poa_index');
        } else return abort(403);
    }

    /**
     * Vista para cargar POA
     *
     * @return \Illuminate\Http\Response
     */
    public function loadPOA()
    {
        if (Auth::user()->can('importa-esigef')) {
            $year = Carbon::now()->year;
            $ejercicio = Apertura::first();
            if (count($ejercicio) > 0) {
//                if ($ejercicio->ejercicio == $year) {
                $cargar = false;
//                } else $cargar = true;
            } else $cargar = true;

            $poa = '';
            return view('configuracion.apertura', compact('cargar', 'ejercicio', 'year', 'poa'));
        } else return abort(403);
    }

    /**
     * Importar POA
     */
    public function importPOA(Request $request)
    {
        if (Auth::user()->can('importa-esigef')) {
            $ejercicio = Apertura::first();
            if (count($ejercicio) > 0) {
                return response()->json([
                    "response" => "Hay un ejercicio cargado"
                ]);
            }

            if ($request->hasFile('poa_file')) {
                $file = $request->file('poa_file');
                $path = $file->getRealPath();
                $data = Excel::load($path, function ($reader) {
                })->get();
                //LLenar tabla carga_inicial
                $insert = [];
                if (!empty($data) && $data->count()) {
                    try {
                        DB::beginTransaction();
                        foreach ($data as $key => $value) {
                            if ($value->programa != '') {
                                $insert[] = [
                                    "ejercicio" => $value['ejercicio'],
                                    //"cod_entidad" => $value->entidad,
                                    //"u_ejec" => $value->unidad_ejecutora,
                                    // "u_desc" => $value->unidad_desconcentrada,
                                    "programa" => $value->programa,
                                    "nombre_programa" => $value->nombre_programa,
                                    //"sub_prog" => $value->subprograma,
                                    // "proyecto" => $value->proyecto,
                                    "actividad" => $value['actividad'],
                                    "nombre_actividad" => $value->nombre_actividad,
                                    // "obra" => $value->obra,
                                    "renglon" => $value->renglon,
                                    "nombre_item" => $value->nombre_item,
                                    //"geografico" => $value->geografico,
                                    //"fuente" => $value->fuente,
                                    //"organismo" => $value->organismo,
                                    //"correlativo" => $value->correlativo,
                                    // "nomb_entidad" => $value->nombre_entidad,
                                    //"nomb_geo" => $value->nombre_geografico,
                                    //"asignado" => $value->asignado,
                                    "codificado" => str_replace(',', '.', $value->codificado),
                                    "reserv_neg" => str_replace(',', '.', $value->reservado_negativo),
                                    "precompromiso" => str_replace(',', '.', $value->precompromiso),
                                    "compromiso" => str_replace(',', '.', $value->compromiso),
                                    "devengado" => str_replace(',', '.', $value->devengado),
                                    "pagado" => str_replace(',', '.', $value->pagado),
                                    "disponible" => str_replace(',', '.', $value->saldo_disponible),
                                    "no_proyecto" => str_replace(',', '.', $value->no_proyecto),
                                ];
                            }
                        }
                        if (!empty($insert)) {
                            DB::table('carga_inicial')->insert($insert);
                        }

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
            }
        } else return abort(403);

    }

    /**
     * Reiniciar ejercicio
     * @param Request $request
     */
    public function resetPOA(Request $request)
    {
        if (Auth::user()->can('importa-esigef')) {
            if ($request->get('reset') == "on") {

                if (Schema::hasTable('carga_inicial')) {
                    Apertura::truncate();
                    return response()->json([
                        "response" => "Se reinicio el ejercicio"
                    ]);
                } else return response()->json([
                    "response" => "Ah ocurrido un error",
                    "tipo" => "error"
                ]);
            } else return back();
        } else return abort(403);

    }

    /**
     * Guardar Configuracion
     *
     * @return \Illuminate\Http\Response
     */

    public
    function getConfig()
    {
        $config = Configuration::all();
        return view('configuracion.config', compact('config'));
    }

    /**
     * Guardar Configuracion
     *
     * @return \Illuminate\Http\Response
     */

    public function postConfig(Request $request)
    {
        $config = new Configuration();
        $config->iva = $request->get('iva');
        $config->year = $request->get('year');
        $config->save();

        return redirect()->back();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
