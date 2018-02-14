<?php

namespace App\Http\Controllers;

use App\Area;
use Illuminate\Http\Request;
use App\Http\Requests\AreaStoreRequest;
use App\Http\Requests\AreaUpdateRequest;
use App\Http\Requests;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador'], ['except' => ['index']]);
    }

    /**
     * Lista el contenido todaslas areas y refresca el contenido de la seccion content
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $areas = Area::all();
        $view=view('areas.index',compact('areas'));
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
    public function store(AreaStoreRequest $request)
    {
        $area=new Area();
        $area->area=strtoupper($request->get('area'));
        $area->save();
        $view=view('areas.index');
        $message="Area \" $area->area \" creada";
        if ($request->ajax()){
            return response()->json(['message'=>$message]);
        }else return redirect()->route('admin.areas.index')->with('message',$message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $area=Area::findOrFail($id);
        return response()->json($area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AreaUpdateRequest $request, $id)
    {
        $area=Area::findOrFail($id);
        $area->area=strtoupper($request->get('area'));
        $result=$area->update();
        $message="Area \"$area->area\" actualizada";
        if ($request->ajax()){
            if ($result){
                return response()->json([
                    "message"=>$message,
                    "estado"=>'success'
                ]);
            } else{
                $message="Error al actualizar el área";
                return response()->json(["message"=>$message,"estado"=>'error']);
            }
        }
        else   return redirect()->route('admin.areas.index')->with('message',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $area=Area::findOrFail($id);
        $area->delete();
        $message='Area '.$area->area. ' eliminada';
        if ($request->ajax()){
            return response()->json(['message'=>$message]);
        }
    }
}
