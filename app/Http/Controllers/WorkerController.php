<?php

namespace App\Http\Controllers;

use App\Area;
use App\Departamento;
use App\Events\UserCreated;
use App\User;
use App\Worker;
use Illuminate\Http\Request;
use DB;
use Event;

use App\Http\Requests\WorkerStoreRequest;
use App\Http\Requests\WorkerUpdateRequest;

use App\Http\Requests;

class WorkerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|responsable-poa|administrador']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $workers = Worker::with('departamento', 'pacs')->get();
//        $workers=DB::table('workers as w')
//            ->join('areas as a','a.id','=','w.area_id')
//            ->select('w.id','nombres','apellidos','num_doc','cargo','d.departamento','email','a.area')
//            ->get();

        $user=$request->user();
        $area_id=$user->worker->departamento->area_id;

        if ($user->hasRole('root') || $user->hasRole('administrador')){
            $areas_coll = Area::all();
        } else if ($user->hasRole('responsable-poa')) {
            $areas_coll = Area::where('id',$area_id);
        }
        $list_areas = $areas_coll->pluck('area', 'id');

        $departamento = Departamento::all();
        $list_departamentos = $departamento->pluck('departamento', 'id');

        $view = view('workers.index', compact('workers', 'list_areas', 'list_departamentos'));

        if ($request->ajax()) {
            $sections = $view->rendersections();
            return response()->json($sections['content']);
        } else return $view;
    }

    /**
     * Obtener departamento al seleccionar el area
     */
    public function getDpto(Request $request)
    {
        if ($request->ajax()) {
            $area = Area::where('id', $request->input('area_id'))->first();
            $dpto = $area->departamentos;
            return response()->json($dpto);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkerStoreRequest $request)
    {
        if ($request->input('departamento_id') == 'placeholder') {
            return redirect()->route('admin.workers.index')->withInput()->with('message_danger', 'Seleccione el departamento');
        }
        $departamento = Departamento::findOrFail($request->input('departamento_id'));

        try {
            DB::beginTransaction();

            $worker = new Worker();
            $worker->nombres = strtoupper($request->input('nombres'));
            $worker->apellidos = strtoupper($request->input('apellidos'));
            $worker->email = $request->input('email');
            $worker->num_doc = $request->input('num_doc');
            $worker->cargo = $request->input('cargo');
            $worker->tratamiento = $request->input('tratamiento');
            $worker->departamento()->associate($departamento);
            $worker->save();

            $pass = str_random(6);
            $user = new User([
                'name' => $worker->nombres,
                'password' => $pass,
                'email' => $worker->email,
            ]);
            $user->worker()->associate($worker);
            $user->save();

            // $view = view('workers.index');

            DB::commit();

            Event::fire(new UserCreated($user, $pass));

            $message = "Trabajador creado";
            if ($request->ajax()) {
                return response()->json(['message' => $message]);
            } else return redirect()->route('admin.workers.index')->with('message', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["response" => $e->getMessage(), "tipo" => "error"]);
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
    public function edit($id)
    {
        $worker = Worker::findOrFail($id);
        $dpto = $worker->departamento;
        $area = Area::where('id', $dpto->area_id)->first();
        return response()->json(["worker" => $worker, "area" => $area]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(WorkerUpdateRequest $request, $id)
    {
        if ($request->input('dep_id') == 'placeholder') {
            return redirect()->route('admin.workers.index')->withInput()->with('message_danger', 'Seleccione el departamento');
        }

        try {
            DB::beginTransaction();

        $worker = Worker::findOrFail($id);
        $departamento = Departamento::findOrFail($request->input('dep_id'));
        $worker->nombres = strtoupper($request->input('nombres'));
        $worker->apellidos = strtoupper($request->input('apellidos'));
        $worker->email = $request->input('email');
        $worker->num_doc = $request->input('num_doc');
        $worker->cargo = $request->input('cargo');
        $worker->tratamiento = $request->input('tratamiento');
        $worker->departamento()->associate($departamento);
        $worker->update();

        $user=User::where('worker_id',$worker->id)->first();
        $user->name=$worker->nombres;
        $user->email=$worker->email;
        $user->update();

            DB::commit();

        $message = "Trabajador actualizado";
        if ($request->ajax()) {
            return response()->json([
                "message" => $message,
                "estado" => 'success'
            ]);
        } else return redirect()->route('admin.workers.index')->with('message', $message);

        } catch (\Exception $e) {
            DB::rollback();
//            return response()->json(["response" => $e->getMessage(), "tipo" => "error"]);
            $message = "Error al actualizar al trabajador";
            if ($request->ajax()) {
                return response()->json(["message" => $message, "estado" => 'error']);
            } else return redirect()->route('admin.workers.index')->with('message_danger', $message);
        }

    }

    /**
     * Delete worker and his user only if role is root
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user_login = $request->user();
        if ($user_login->hasRole('root')) {
            try {
                DB::beginTransaction();

                $worker = Worker::findOrFail($id);
                $user = User::where('worker_id', $worker->id)->first();
                $user->delete();
                $worker->delete();

                DB::commit();

                $message = 'Trabajador: ' . $worker->nombres . ' eliminado';
                if ($request->ajax()) {
                    return response()->json(['message' => $message]);
                }

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(["response" => $e->getMessage(), "tipo" => "error"]);
            }
        }else return abort(403);
    }

}
