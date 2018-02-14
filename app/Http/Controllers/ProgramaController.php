<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Apertura;
use App\Programa;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;

use App\Http\Requests\ProgramaUpdateRequest;
use App\Http\Requests\ProgramaStoreRequest;

class ProgramaController extends Controller
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
        $programas = Programa::with('actividads')->get();

        $actividades = Actividad::all();
//        $list_actividades = $actividades->pluck('actividad', 'id');

        $view = view('programas.index', compact('programas', 'actividades'));
        if ($request->ajax()) {
            $sections = $view->rendersections();
            return response()->json($sections['content']);
        } else return $view;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProgramaStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $programa = new Programa();
            $programa->programa = strtoupper($request->get('programa'));
            $programa->cod_programa = strtoupper($request->get('cod_programa'));
            $programa->save();
            $view = view('programas.index');
            $message = "Programa creado";

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => $message, "estado" => 'success']);
            } else return $view;

        } catch (\Exception $e) {

            DB::rollback();
            $message = "Error al insertar el programa en la BBDD";
            if ($request->ajax()) {
                return response()->json(["message" => $message, "estado" => 'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            } else {
                return redirect()->route('admin.programas.index')->with('message_danger', $message)->withInput();
//                return redirect()->route('admin.programas.index')->with('message_danger',$e->getMessage())->withInput();
            }
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $programa = Programa::findOrFail($id);

        return response()->json($programa);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProgramaUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $prog = Programa::findOrFail($id);
            $prog->cod_programa = strtoupper($request->get('cod_programa'));
            $prog->programa = strtoupper($request->get('programa'));
            $prog->update();

            DB::commit();

            $mensaje = "Programa actualizado";
            if ($request->ajax()) {
                return response()->json([
                    "message" => $mensaje,
                    "estado" => 'success'
                ]);
            }

        } catch (\Exception $e) {

            DB::rollback();
            $message = "Error al actualizar el programa en la BBDD";
            if ($request->ajax()) {
                return response()->json(["message" => $message, "estado" => 'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            } else {
                return redirect()->route('admin.programas.index')->with('message_danger', $message)->withInput();
//                return redirect()->route('admin.programas.index')->with('message_danger',$e->getMessage())->withInput();
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
        $programa = Programa::findOrFail($id);
        $programa->delete();
        $message = 'Programa ' . $programa->programa . ' eliminado';
        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadActividades(Request $request, $id)
    {
        $programa = Programa::where('id', $id)->first();
        $actividades = Actividad::all();
        return view('programas.listActividades', compact('programa', 'actividades'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function asociarActividades(Request $request)
    {
        try {
            DB::beginTransaction();
            $programa = Programa::where('id', $request->input('program_id'))->first();
            $actividades = $request->input('actividads');

            if (count($actividades) > 0) {
                $programa->actividads()->sync($actividades);
            } else $programa->actividads()->detach();


            DB::commit();
            $mensaje = "Actividades actualizadas";
            if ($request->ajax()) {
                return response()->json([
                    "message" => $mensaje,
                    "estado" => 'success'
                ]);
            }

        } catch (\Exception $e) {

            DB::rollback();
            $message = "Error al sincronizar las actividades y los programas en la BBDD";
            if ($request->ajax()) {
                return response()->json(["message" => $message, "estado" => 'error']);
//                return response()->json([ "message" => $e->getMessage(),"estado" => "error"]);
            } else {
                return redirect()->route('admin.programas.index')->with('message_danger', $message)->withInput();
//                return redirect()->route('admin.programas.index')->with('message_danger',$e->getMessage())->withInput();
            }
        }


    }


}
