<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Apertura;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;

use App\Http\Requests\ActividadStoreRequest;
use App\Http\Requests\ActividadUpdateRequest;

class ActividadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador'], ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $actividades = Actividad::all();
        $view=view('actividades.index',compact('actividades'));
        if ($request->ajax()){
            $sections=$view->rendersections();
            return response()->json($sections['content']);
        }
        else return $view;
    }



    /**
     * Importar actividades desde poa inicial
     * @param Request $request
     * @return mixed
     */
    public function importAct(Request $request)
    {
        $act = Apertura::select('actividad')->distinct()->get();

        $insert = [];
        if (count($act)>0) {
            foreach ($act as $key => $value) {
                $insert[] = [
                    "codigo" => $value->actividad,
                ];
            }
            if (!empty($insert)) {
                DB::table('actividads')->insert($insert);
            }
        }

        return response()->json([
            "response" => "Registros cargados"
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActividadStoreRequest $request)
    {
        $actividad=new Actividad();
        $actividad->actividad=strtoupper($request->get('actividad'));
        $actividad->cod_actividad=strtoupper($request->get('cod_actividad'));
        $actividad->save();
        $view=view('actividades.index');
        $message="Actividad creada";
        if ($request->ajax()){
            return response()->json(['message'=>$message,"estado" => 'success']);
        }else return $view;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $actividad=Actividad::findOrFail($id);

        return response()->json($actividad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActividadUpdateRequest $request, $id)
    {
        $act=Actividad::findOrFail($id);
        $act->cod_actividad=strtoupper($request->get('cod_actividad'));
        $act->actividad=strtoupper($request->get('actividad'));
        $result=$act->update();
        if ($request->ajax()){
            if ($result){
                $mensaje="Actividad actualizada";
                return response()->json([
                    "message"=>$mensaje,
                    "estado"=>'success'
                ]);
            } else{
                return response()->json(["estado"=>'error']);
            }
        }
        else { return $result;}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $actividad=Actividad::findOrFail($id);
        $actividad->delete();
        $message='Actividad '.$actividad->actividad. ' eliminada';
        if ($request->ajax()){
            return response()->json(['message'=>$message]);
        }
    }
}
