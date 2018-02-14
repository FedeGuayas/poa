<?php

namespace App\Http\Controllers;

use App\Area;
use App\Departamento;
use Illuminate\Http\Request;
use App\Http\Requests\DepartamentoStoreRequest;
use App\Http\Requests\DepartamentoUpdateRequest;
use App\Http\Requests;

use DB;
use Illuminate\Support\Facades\Auth;

class DepartamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|responsable-poa|administrador'], ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user=$request->user();
        $area_id=$user->worker->departamento->area_id;

        if ($user->hasRole('root') || $user->hasRole('administrador')){
            $areas_coll = Area::all();
        } else  {
            $areas_coll = Area::where('id',$area_id);
        }
        $list_areas = $areas_coll->pluck('area', 'id');

        $departamentos=Departamento::with('area')->orderBy('area_id')->get();


        $view=view('departamentos.index',compact('departamentos','list_areas'));
        if ($request->ajax()){
            $sections=$view->rendersections();
            return response()->json($sections['content']);
        }else return $view;
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartamentoStoreRequest $request)
    {
        $area=Area::findOrFail($request->area);
        try {
            DB::beginTransaction();

            $dep=new Departamento();
            $dep->departamento=strtoupper($request->get('departamento'));
            $dep->area()->associate($area);
            $dep->save();
            $view=view('departamentos.index');
            $message="Departamento \" $dep->departamento \" creado";

            DB::commit();
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'success']);
            }
            else  return redirect()->route('admin.departamentos.index')->with('message',$message);

        } catch (\Exception $e) {

            DB::rollback();
            $message="Error al insertar los datos en la BBDD";
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            }else{
                return redirect()->route('admin.departamentos.index')->with('message_danger',$message)->withInput();
//                return redirect()->route('admin.departamentos.index')->with('message_danger',$e->getMessage())->withInput();
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $departamento=Departamento::findOrFail($id);
        
        return response()->json($departamento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DepartamentoUpdateRequest $request, $id)
    {
        $dep=Departamento::findOrFail($id);
        try {
            DB::beginTransaction();
            $dep->departamento=strtoupper($request->get('departamento'));
            $area=$request->get('area');
            $dep->area()->associate($area);
            $result=$dep->update();
            $message="Departamento \" $dep->departamento \" actualizado";

            DB::commit();
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'success']);
            }
            else  return redirect()->route('admin.departamentos.index')->with('message',$message);

        } catch (\Exception $e) {

            DB::rollback();
            $message="Error al insertar los datos en la BBDD";
            if ($request->ajax()){
                return response()->json(["message"=>$message, "estado"=>'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            }else{
                return redirect()->route('admin.departamentos.index')->with('message_danger',$message)->withInput();
//                return redirect()->route('admin.departamentos.index')->with('message_danger',$e->getMessage())->withInput();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $dep=Departamento::findOrFail($id);
        $dep->delete();
        $message='Departamento '.$dep->departamento. ' eliminado';
        if ($request->ajax()){
            return response()->json(['message'=>$message]);
        }
    }
}
