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

        try {
            DB::beginTransaction();
            $item = Item::where('id', $request->input('item'))->first();
            $areas = $request->input('area_id');
            $meses = $request->input('mes');
            $montos = $request->input('subtotal_id');

            $cont = 0;
            while ($cont < count($areas)) {

                $area_item=AreaItem::where('item_id',$item->id)->where('area_id',$areas[$cont])->where('mes',$meses[$cont])->first();

//                $post->comments()->saveMany([
//                    new App\Comment(['message' => 'A new comment.']),
//                    new App\Comment(['message' => 'Another comment.']),
//                ]);
                if ($area_item){
                    $area_item->extras()->create([
                            "area_id" =>$areas[$cont],
                            'monto' => $montos[$cont],
                            'mes' => $meses[$cont],
                            'item_id' => $item->id]
                    );
                }else{
                    return redirect()->route('admin.ingresos.index')->with('message_danger', 'Error no existe el POA al que esta agregando ingresos');
                }
                $cont++;
            }

            DB::commit();
            return redirect()->route('admin.ingresos.index')->with('message', 'Ingresos guardados');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.ingresos.index')->with('message_danger', 'Error no se guardaron los registros');
//                return redirect()->route('poaFDG')->with('message_danger',$e->getMessage());
//                return response()->json([ "response" => $e->getMessage(),"tipo" => "error"]);
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

    public function loadExtra(Request $request)
    {
        $area_id = $request->input('area_id');
        $item_id = $request->input('item_id');
        $extras = DB::table('extras as e')
            ->join('months as m','m.cod','=','e.mes')
            ->select('m.month as mes','area_item_id',DB::raw('sum(monto) monto'))
            ->where('area_id', $area_id)
            ->where('item_id', $item_id)
            ->groupBy('area_item_id')
            ->get();
        $total = 0;
        foreach ($extras as $ex) {
            $total = $total + $ex->monto;
        }
        return view('extras.listExtra', compact('extras', 'total'));
    }
}
