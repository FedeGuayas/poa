<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaItem;
use App\Item;
use App\Month;
use App\Programa;
use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Illuminate\Support\Facades\Auth;

class PoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador'], ['except' => ['index', 'getItem', 'getUniqueItem']]);
    }

    /**
     * Vista que muestra los presupuestos por areas
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('planifica-pac')) {
//            $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
//            $list_programs = $programas->pluck('programa', 'id');

            $areas = Area::all();
            $list_areas = $areas->pluck('area', 'id');
            $area_select = $request->input('area');
            $area = Area::where('id', $area_select)->first();

            $area_item = DB::table('area_item as ai')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('months as m', 'm.cod', '=', 'ai.mes')
                ->select('ai.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'ai.monto', 'm.month', 'a.area')
//            ->where('area_id','like','%'.$area_select.'%')
                ->where('area_id', $area_select)
                ->get();

            $total = 0;
            foreach ($area_item as $ai) {
                $total = $total + $ai->monto;
            }
            return view('poafdg.index', compact('area_item', 'total', 'list_areas', 'area_select', 'area'));
        } else return abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $poa = AreaItem::findOrFail($id);
        return response()->json($poa);
    }

    /**
     *
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();


            $poafdg = AreaItem::findOrFail($id);
            $monto = $poafdg->monto;

            $item = Item::where('id', $poafdg->item_id)->first();
            $disponible = $item->disponible;

            $nuevo_valor=$request->input('monto');

            if ($nuevo_valor == $monto) {
                return response()->json(['message' => 'No se realizaron cambios']);
            }

            $max=$monto+$disponible;

            if ($nuevo_valor > $monto) {
                $dif = $nuevo_valor - $monto;
                $disp = $disponible - $dif;//disp de del item general
            } else if ($nuevo_valor < $monto) {
                $dif = $monto - $nuevo_valor;
                $disp = $disponible + $dif;
            } else $disp = $disponible;

            DB::commit();
            if ($nuevo_valor <= $max) {
                $poafdg->monto = $nuevo_valor;
                $poafdg->update();
                $item->disponible = $disp;
                $item->update();
                $message = 'Se actualizo el valor correctamente';
                return response()->json(['message' => $message]);
            } else return response()->json(['message' => 'No se pudo actualizar el valor']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()]);
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
        $poafdg = AreaItem::findOrFail($id);
        $monto = $poafdg->monto;

        $mes=Month::where('cod',$poafdg->mes)->first();

        $item_id = $poafdg->item_id;
        $item = Item::where('id', $item_id)->first();
        $disponible = $item->disponible;
        $disp = $disponible + $monto;//disp de del item general
        $item->disponible = $disp;
        $item->update();

        $poafdg->delete();
        $message = 'Monto del mes ' . $mes->month . ' eliminado';
        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
    }


    /**
     * Programacion del Poa de FDG cargar programas y actividades para select dinamico
     * @param Request $request
     * @return mixed
     */
    public function poaFDG(Request $request)
    {

        $programas = Programa::select(DB::raw('concat (cod_programa," - ",programa) as programa,id'))->get();
        $list_programs = $programas->pluck('programa', 'id');

        $areas = Area::all();
        $list_areas = $areas->pluck('area', 'id');

        $meses = Month::select(DB::raw('month,cod'))->get();
        $list_meses = $meses->pluck('month', 'cod');

        $mes = $request->input('mes');

        if ($request->ajax()) {
            $prog_id = $request->get('prog_id');
            $prog = Programa::where('id', $prog_id)->first();
            $actividad_list = $prog->actividads;

            return response()->json($actividad_list);
        }
        return view('poafdg.planificacion', compact('list_programs', 'list_areas', 'list_meses', 'mes'));

    }


    /**
     * Programacion del Poa de FDG cargar item al seleccionar la actividad, select dinamico
     */
    public function getItem(Request $request)
    {

        if ($request->ajax()) {
            $prog_id = $request->input('prog_id');
            $act_id = $request->input('act_id');

            $act_prog_id = DB::table('actividad_programa')->where('actividad_id', $act_id)->where('programa_id', $prog_id)->first();

            $items = Item::where('actividad_programa_id', $act_prog_id->id)->get();

            return response()->json($items);
        }
    }

    /**
     * Codigo unico de programa-actividad-item
     * @param Request $request
     * @return mixed
     */
    public function getUniqueItem(Request $request)
    {
        $item_id = $request->input('item_id');
        $item = Item::with('extras', 'areas')->where('id', $item_id)->first();

        if ($request->ajax()) {
            return response()->json($item);
        }
    }


    /**
     *  Cargar el dinero DEL POA repartido para esa area
     * @param Request $request
     * @return mixed
     */
    public function loadItemArea(Request $request)
    {
        $area_id = $request->input('area_id');
        $item_id = $request->input('item_id');
        $area_item = DB::table('area_item as ai')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->select('ai.id', 'ai.item_id', 'ai.monto', 'ai.mes', 'ai.area_id', 'ai.item_id', 'm.month')
            ->where('area_id', $area_id)
            ->where('item_id', $item_id)
            ->get();
        $total = 0;
        foreach ($area_item as $ai) {
            $total = $total + $ai->monto;
        }
        return view('poafdg.listItemArea', compact('area_item', 'total'));
    }

    /**
     * Guardar planificacion del poa del areaa
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storePlanificacion(Request $request)
    {
        if ($request->input('resto') >= 0) {
            try {
                DB::beginTransaction();
                $item = Item::where('id', $request->input('item'))->first();
                $areas = $request->input('area_id');
                $meses = $request->input('mes');
                $montos = $request->input('subtotal_id');

                $nueva_disponibilidad = $request->input('resto');
                if ($nueva_disponibilidad >= 0) {
                    $item->disponible = $nueva_disponibilidad;
                    $item->update();
                } else {
                    return back()->with('message_danger', 'No debe superrar la disponibilidad de recursos');
                }

                $cont = 0;
                while ($cont < count($areas)) {
                    $item->areas()->attach($areas[$cont], ['monto' => $montos[$cont], 'mes' => $meses[$cont]]);
                    $cont++;
                }

                DB::commit();
                return redirect()->route('poaFDG')->with('message', 'Recursos guardados');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('poaFDG')->with('message_danger', 'Error no se guardaron los registros en la BBDD');
//                return redirect()->route('poaFDG')->with('message_danger',$e->getMessage());
//                return response()->json([ "response" => $e->getMessage(),"tipo" => "error"]);
            }
        } else
            return redirect()->route('poaFDG')->with('message_danger', 'No debe superar el monto disponible')->withInput();
    }

}