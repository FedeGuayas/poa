<?php

namespace App\Http\Controllers;

use App\Apertura;
use App\Month;
use App\Reforma;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;

use App\Http\Requests;

use DB;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**Resumen por mes y area
     * @param Request $request
     */
    public function resumenMensual(Request $request)
    {
        if (Auth::user()->can('consultor')) {
            setlocale(LC_TIME, 'es');

            $meses = Month::select(DB::raw('month,cod'))->get();
            $list_meses = $meses->pluck('month', 'cod');

            $mes = $request->input('mes');

            $fecha_actual = Carbon::now();
//        $month = $fecha_actual->formatLocalized('%B');
            $month = $fecha_actual->month;

            if ($mes === null) {
                $mes = $month;
            }

//             DB::select( DB::raw("SELECT * FROM some_table WHERE some_col = '$someVariable'")

//        DB::table('users')
//            ->select('first_name', 'TotalCatches.*')
//
//            ->join(DB::raw('(SELECT user_id, COUNT(user_id) TotalCatch, DATEDIFF(NOW(), MIN(created_at)) Days, COUNT(user_id)/DATEDIFF(NOW(), MIN(created_at)) CatchesPerDay FROM `catch-text` GROUP BY user_id) TotalCatches'), function($join)
//            {
//                $join->on('users.id', '=', 'TotalCatches.user_id');
//            })
//            ->orderBy('TotalCatches.CatchesPerDay', 'DESC')
//            ->get();


//        "'Select cod_programa,cod_actividad,cod_item,planificado,extra, planificado+extra total,devengado from areas a
// left join
//        (select cod_programa,cod_actividad,cod_item,area_id,mes,sum(monto) planificado from area_item
//   inner join items i on i.id=area_item.item_id
//   GROUP BY area_id,mes ) ai on a.id=ai.area_id
// left join
//        (select mes,area_id,sum(monto) extra from extras GROUP BY area_id,mes ) e on e.area_id=a.id
//left join
//        (select programa,actividad,renglon,devengado from carga_inicial) ci on (ci.programa=ai.cod_programa and ci.actividad=ai.cod_actividad and ci.renglon=ai.cod_item)
// where ai.mes='SEPTIEMBRE'"

//        DB::raw('IFNULL(planificado+extra, planificado) total')

            $resumen = DB::table('areas as a')
                ->select('cod_programa', 'cod_actividad', 'cod_item', 'planificado', 'area', 'ai.mes', DB::raw('IFNULL(extra, 0) extra'), DB::raw('IFNULL(planificado+extra, planificado) total'), 'devengado')
//            ->where('ai.mes', 'like', '%' . $mes . '%')
                ->join(DB::raw('
                (SELECT ai.id,cod_programa,cod_actividad,cod_item,ai.area_id,mes,sum(monto) planificado, sum(devengado) devengado FROM area_item ai                  INNER JOIN items i ON i.id=ai.item_id 
                INNER JOIN carga_inicial ci ON (i.cod_programa=ci.programa and i.cod_actividad=ci.actividad and i.cod_item=ci.renglon) 
                GROUP BY area_id,mes) 
                ai'
                ), function ($join) {
                    $join->on('a.id', '=', 'ai.area_id');
                })
                ->leftJoin(DB::raw('(SELECT area_item_id,mes,area_id,sum(monto) extra from extras GROUP BY area_id,mes) e'), function ($join) {
                    $join->on('e.area_id', '=', 'ai.area_id');
                    $join->on('e.mes', '=', 'ai.mes');
                })
                ->where('ai.mes', '=', $mes)
                ->get();


//dd($resumen);
            $devengado_pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->join('departamentos as d', 'd.id', '=', 'w.departamento_id')
                ->select('p.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'p.mes', 'p.presupuesto', 'p.disponible', 'p.comprometido', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto','d.area_id as area_trabajador')
//            ->where('ai.mes', 'like', '%' . $mes . '%')
                ->where('ai.mes', '=', $mes)
                ->get();


//        $total_esigef = Apertura::sum('devengado');
//        $total_p = 0;
//        $total_e = 0;
//        foreach ($resumen as $r) {
//            $total_p = $total_p + $r->planificado;
////            $total_e = $total_e + $r->extra;
//        }
//        $total_mes = $total_p + $total_e;

            $view = view('reportes.resumen_mensual', compact('mes', 'list_meses', 'resumen', 'devengado_pacs'));
            if ($request->ajax()) {
                $sections = $view->rendersections();
                return response()->json($sections['content']);
            } else return $view;
        } else return abort(403);
    }


    /**
     * imprimir reforma en pdf
     * @param $id
     * @return mixed
     */
    public function reformaPDF($id)
    {
//            $reforma = Reforma::with('pac_origen','pac_destino')
//                ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
//                ->join('items as i', 'i.id', '=', 'ai.item_id')
//                ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
//                ->join('programas as p', 'p.id', '=', 'ap.programa_id')
//                ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
//                ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'ai.mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto')
//                ->where('id', $id)->first();

        $reforma = DB::table('reformas as r')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto','ai.area_id')
            ->where('r.id', $id)->first();


        $area_id = $reforma->area_id;

        //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
        $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
            $query->where('area_id', $area_id)
                ->where('departamento', 'like', "%direcc%");
        })->first();

        $detalles_o = DB::table('reformas as r')
            ->join('pac_origen as po', 'po.reforma_id', '=', 'r.id')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->join('pacs as pac', 'pac.id', '=', 'po.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
            ->select('po.valor_orig', 'p.programa', 'a.actividad', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'm.month as mes')
            ->where('r.id', $id)
            ->get();

        $detalles_d = DB::table('reformas as r')
            ->join('pac_destino as pd', 'pd.reforma_id', '=', 'r.id')
            ->join('pacs as pac', 'pac.id', '=', 'pd.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
            ->join('area_item as ai', 'ai.id', '=', 'pac.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'm.month as mes', 'pac.id', DB::raw('sum(pd.valor_dest) as valor_dest'))
//                ->groupBy('i.cod_programa','i.cod_actividad','i.cod_item')
            ->groupBy('pac.id')
            ->where('r.id', $id)
            ->get();

//            dd($detalles_d);

        //setlocale(LC_TIME, 'es');
        //$fecha_actual = Carbon::now();
        //$month = $fecha_actual->formatLocalized('%B');//mes en español
        //$day = $fecha_actual->format('d');
        //$year = $fecha_actual->format('Y');
        //$date = $fecha_actual->format('Y-m-d');

//            PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        $pdf = PDF::loadView('reportes.reforma-pdf', compact('reforma', 'detalles_o', 'detalles_d','jefe_area'));
        //        return $pdf->download('Refroma.pdf');//descarga el pdf
        return $pdf->setPaper('letter', 'landscape')->stream('Reforma');//imprime en pantalla

    }


    /**
     * imprimir las refroamas seleccionadas en pdf
     * @param $id
     * @return mixed
     */
    public function reformaSelectPDF(Request $request)
    {
        if (count($request->input('imp_reformas')) == 0) {
            return redirect()->back()->with('message_danger', 'Debe seleccionar al menos una reforma para poder imprmir');
        } else $reformas_id = $request->input('imp_reformas');//arreglo con los id de refromas

        $reforma = DB::table('reformas as r')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->join('reform_type as rt', 'rt.id', '=', 'r.reform_type_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'rt.tipo_reforma', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto','ai.area_id')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('r.id', 'desc')
            ->get();

        $area_id = $request->user()->worker->departamento->area_id;

        //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
        $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
            $query->where('area_id', $area_id)
                ->where('departamento', 'like', "%direcc%");
        })->first();

        $collection = Collection::make($reforma);
        $total_reforma = $collection->sum('monto_orig');

        $detalles_o = DB::table('reformas as r')
            ->join('pac_origen as po', 'po.reforma_id', '=', 'r.id')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->join('pacs as pac', 'pac.id', '=', 'po.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
            ->select('po.valor_orig', 'p.programa', 'a.actividad', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'm.month as mes')
            ->whereIn('r.id', $reformas_id)
//            ->where('r.id', $id)
            ->orderBy('r.id', 'desc')
            ->get();

        $detalles_d = DB::table('reformas as r')
            ->join('pac_destino as pd', 'pd.reforma_id', '=', 'r.id')
            ->join('pacs as pac', 'pac.id', '=', 'pd.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
            ->join('area_item as ai', 'ai.id', '=', 'pac.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'm.month as mes', 'pd.valor_dest')
//            ->groupBy('i.cod_programa','i.cod_actividad','i.cod_item')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('pd.id', 'desc')
            ->get();


//        SELECT pd.id,pd.reforma_id,pd.pac_id,pac.area_item_id,o.valor_orig,o.cod_programa,o.cod_actividad,o.programa,o.actividad,o.cod_item,o.item,o.mes,i.cod_programa,i.cod_actividad,p.programa,a.actividad,pac.cod_item,pac.item,pac.mes,pd.valor_dest FROM pac_destino pd
//join pacs pac on pac.id =pd.pac_id
//join area_item ai on ai.id=pac.area_item_id
//join items i on i.id=ai.item_id
//join programas p on p.cod_programa=i.cod_programa
//join actividads a on a.cod_actividad=i.cod_actividad
//left join
//    (SELECT po.id,po.reforma_id,po.pac_id,pac.area_item_id,i.cod_programa,i.cod_actividad,p.programa,a.actividad,pac.cod_item,pac.item,pac.mes,po.valor_orig FROM pac_origen po
//join pacs pac on pac.id =po.pac_id
//join area_item ai on ai.id=pac.area_item_id
//join items i on i.id=ai.item_id
//join programas p on p.cod_programa=i.cod_programa
//join actividads a on a.cod_actividad=i.cod_actividad order by po.reforma_id) o on pd.reforma_id=o.reforma_id
//where pd.reforma_id=8 or pd.reforma_id=9
//Order By pd.reforma_id desc ;

        $todas = DB::table('pac_destino as pd')
            ->select('o.valor_orig', 'o.cod_programa as cod_programa_o', 'o.cod_actividad as cod_actividad_o', 'o.programa as programa_o', 'o.actividad as actividad_o', 'o.cod_item as cod_item_o', 'o.item as item_o', 'o.mes as mes_o', 'i.cod_programa', 'i.cod_actividad', 'p.programa', 'a.actividad', 'pac.cod_item', 'pac.item', 'm.month as mes', 'pd.valor_dest')
            ->join('pacs as pac', 'pac.id', '=', 'pd.pac_id')
            ->join('months as m', 'm.cod', '=','pac.mes')
            ->join('area_item as ai', 'ai.id', '=', 'pac.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('programas as p', 'p.cod_programa', '=', 'i.cod_programa')
            ->join('actividads as a', 'a.cod_actividad', '=', 'i.cod_actividad')
            ->leftJoin(DB::raw('(
            SELECT po.id,po.reforma_id,po.pac_id,i.cod_programa,i.cod_actividad,p.programa,a.actividad,pac.cod_item,pac.item,m.month as mes,po.valor_orig
             FROM pac_origen po 
             join pacs pac on pac.id =po.pac_id 
             join months m on m.cod =pac.mes 
             join area_item ai on ai.id=pac.area_item_id 
             join items i on i.id=ai.item_id 
             join programas p on p.cod_programa=i.cod_programa 
             join actividads a on a.cod_actividad=i.cod_actividad 
             order by po.reforma_id) o'), function ($join) {
                $join->on('pd.reforma_id', '=', 'o.reforma_id');
            })
            ->whereIn('pd.reforma_id', $reformas_id)
            ->orderBy('pd.reforma_id', 'desc')
            ->get();


        $pdf = PDF::loadView('reportes.reforma_select_pdf', compact('reforma', 'detalles_o', 'detalles_d', 'total_reforma', 'todas','jefe_area'));
        return $pdf->setPaper('letter', 'landscape')->stream('Reforma');//imprime en pantalla


    }
}
