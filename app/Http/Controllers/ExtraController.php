<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Item;
use App\Month;
use App\Programa;
use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Json;

class ExtraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador']);
    }
    /**
     *
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
        $list_programs = $programas->pluck('programa', 'id');

        $areas = Area::all();
        $list_areas = $areas->pluck('area', 'id');

        $meses =Month::select(DB::raw('month,cod'))->get();
        $list_meses = $meses->pluck('month', 'cod');

        $mes = $request->input('mes');

        if ($request->ajax()) {
            $prog_id = $request->get('prog_id');
            $prog = Programa::where('id', $prog_id)->first();
            $actividad_list = $prog->actividads;

            return response()->json($actividad_list);
        }
        return view('extras.extras', compact('list_programs', 'list_areas','list_meses','mes'));
    }
    
    /**
     * Guardar los ingresos extras
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'area_id.*' => 'required',
            'mes.*' => 'required',
            'subtotal_id.*'=> 'required',
            'item'=>'required'
        ];

        $mensajes = [
            'area_id.required' => 'El área es requerida',
            'mes.required' => 'El mes es requerido',
            'subtotal_id.required' => 'El monto es requerido',
            'item.required' => 'No se encontró el item',
        ];

        $validator = Validator::make($request->all(), $rules, $mensajes);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors'=>$validator->messages()], 422);
            } else {
                return back()->withErrors($validator);
            }
        }

        try {
            DB::beginTransaction();
            $item = Item::where('id', $request->input('item'))->first();
            $areas = $request->input('area_id'); //arreglo
            $meses = $request->input('mes');//arreglo
            $montos = $request->input('subtotal_id');//arreglo

            $cont = 0;
            $insert = [];
            while ($cont < count($areas)) {

                if ($areas[$cont] != '' && $meses[$cont] != '' && $montos[$cont] > 0) {
                    $insert[] = [
                        "area_id" =>$areas[$cont],
                        'monto' => $montos[$cont],
                        'mes' => $meses[$cont],
                        'item_id' => $item->id
                    ];
                }

                $cont++;
            }
            if (!empty($insert)) {
                DB::table('extras')->insert($insert);
            }

            DB::commit();
            $message='Ingresos extras guardados correctamente.';
            if ($request->ajax()) {
                return response()->json(['message'=>$message], 200);
            } else {
                return redirect()->route('admin.ingresos.index')->with('message', 'Ingresos extras guardados');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message=$e->getMessage();
            if ($request->ajax()) {
                return response()->json(['message_error'=>'Error crítico: '.$message.'']);
            } else {
                return redirect()->route('admin.ingresos.index')->with('message_danger', $message);
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

    /**
     * Muestra el resumen de ingresos extras para el area seleccionada, no importa el mes que sea
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadExtra(Request $request)
    {
        $area_id = $request->input('area_id');
        $item_id = $request->input('item_id');
        $extras = DB::table('extras as e')
            ->join('months as m','m.cod','=','e.mes')
            ->select('m.month','item_id','area_id','mes',DB::raw('sum(monto) monto'))
            ->where('area_id', $area_id)
            ->where('item_id', $item_id)
            ->groupBy('item_id','area_id','mes')
            ->get();

        $total = 0;
        foreach ($extras as $ex) {
            $total = $total + $ex->monto;
        }
        return view('extras.listExtra', compact('extras', 'total'));
    }
}
