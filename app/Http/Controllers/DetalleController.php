<?php

namespace App\Http\Controllers;

use App\Area;
use App\Departamento;
use App\Detalle;
use App\Pac;
use App\Worker;
use Illuminate\Http\Request;

use App\Http\Requests\GestionStoreRequest;
use App\Http\Requests\GestionUpdateRequest;

use DB;
use App\Http\Requests;

class DetalleController extends Controller
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
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

//        $gestiones=Detalle::all();
            $gestiones = DB::table('detalles as d')
                ->join('pacs as p', 'p.id', '=', 'd.pac_id')
                ->join('months as m', 'm.cod', '=', 'p.mes')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->join('area_item as ai', 'ai.id', '=', 'p.area_item_id')
                ->join('items as i', 'i.id', '=', 'ai.item_id')
                ->select('proveedor', 'num_factura', 'fecha_factura', 'fecha_entrega','p.worker_id', 'importe', 'nota', 'concepto', 'w.nombres', 'w.apellidos', 'p.cod_item', 'p.item', 'i.cod_programa', 'i.cod_actividad', 'p.id', 'd.estado', 'd.id as gestion_id', 'm.month as mes')
//            ->where('p.id',$pac->id)
                ->get();
            return view('detalles.index', compact('gestiones'));

        }else return abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $data = $request->all();
            $pac_id = key($data);
            $pac = Pac::with('worker','area_item')->where('id', $pac_id)->first();

            $area_id=$user_login->worker->departamento->area_id;
            $area = Area::where('id',$area_id)->first();//area ala que pertenece el trabajador

            $workers='';

            if ($area)
            {
                //trabajadores que pertenecen al mismo area del trabajador logeado
                $workers_all = Worker::with('user')->whereHas('departamento', function ($query) use ($area_id){
                    $query->where('area_id',$area_id);
                })->get();

                //trabajadores del mismo area del usuario logeado y que tienen como rol analista(usuario con permisos para reformas)
                $workers = $workers_all->filter(function ($value, $key) {
                    return $value->user->hasRole('analista');
                });
            }

            return view('detalles.create', compact('pac','workers'));

        }else return abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GestionStoreRequest $request)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $pac = Pac::findOrFail($request->input('pac_id'));
            $disponible = $pac->disponible;
            $ejecutado = $pac->comprometido;

            if ($request->input('importe') <= $disponible) {
                $importe = $request->input('importe');
                $disp = $pac->disponible - $importe;
            } else return back()->withInput()->with('message_danger', 'El importe ejecutado no puede ser superior al disponible');

            $pac->disponible = $disp;
            $pac->comprometido = $ejecutado + $importe;

            $detalle = new Detalle();
            $detalle->num_doc = strtoupper($request->input('num_doc'));
            $detalle->proveedor = strtoupper($request->input('proveedor'));
            $detalle->num_factura = strtoupper($request->input('num_factura'));
            $detalle->fecha_factura = $request->input('fecha_factura');
            $detalle->fecha_entrega = $request->input('fecha_entrega');
            $detalle->importe = $importe;
//        $detalle->estado='Pendiente';
            $detalle->nota = strtoupper($request->input('nota'));
            $detalle->pac()->associate($pac);
            $detalle->save();
            $pac->update();
            return redirect()->route('admin.pacs.index')->with('message', 'Gestión guardada correctamente');

        }else return abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pac=Pac::findOrFail($id);
//        $detalles=Detalle::where('pac_id',$pac->id)->get();
        $gestiones=DB::table('detalles as d')
            ->join('pacs as p','p.id','=','d.pac_id')
            ->join('workers as w','w.id','=','p.worker_id')
            ->join('area_item as ai','ai.id','=','p.area_item_id')
            ->join('items as i','i.id','=','ai.item_id')
            ->select('proveedor','num_factura','fecha_factura','fecha_entrega','importe','nota','concepto','w.nombres','w.apellidos','p.cod_item','p.item','i.cod_programa','i.cod_actividad','p.id','d.estado','d.id as gestion_id')
            ->where('p.id',$pac->id)
            ->get();
        return view('detalles.show',compact('gestiones','pac'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $gestion = Detalle::where('id', $id)->first();
            $pac = Pac::where('id', $gestion->pac_id)->first();

            return view('detalles.edit', compact('gestion'));

        }else return abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GestionUpdateRequest $request, $id)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $detalle = Detalle::findOrFail($id);
            $pac = Pac::where('id', $detalle->pac_id)->first();
            $detalle->num_doc = strtoupper($request->input('num_doc'));
            $detalle->proveedor = strtoupper($request->input('proveedor'));
            $detalle->num_factura = strtoupper($request->input('num_factura'));
            $detalle->fecha_factura = $request->input('fecha_factura');
            $detalle->fecha_entrega = $request->input('fecha_entrega');
            $detalle->nota = strtoupper($request->input('nota'));
            $detalle->update();
            return redirect()->route('admin.gestion.show', $pac->id)->with('message', 'Gestión actualizada');

        }else return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $user_login = $request->user();

        if ($user_login->can('gestion-procesos')) {

            $gestion = Detalle::findOrFail($id);

            $pac = $gestion->pac;

            $comprometido = $pac->comprometido - $gestion->importe;
            $disponible = $pac->disponible + $gestion->importe;

            if ($comprometido >= 0) {
                $pac->comprometido = $comprometido;
                $pac->disponible = $disponible;
                $pac->update();
            }

            $gestion->delete();
            $message = 'Gestión eliminada';
            if ($request->ajax()) {
                return response()->json(['message' => $message]);
            }
        }else return abort(403);
    }
}
