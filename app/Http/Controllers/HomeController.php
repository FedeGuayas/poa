<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Month;
use Illuminate\Http\Request;

use Carbon\Carbon;
use DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        setlocale(LC_TIME, 'es');
        $fecha_actual=Carbon::now();
        $mes=$fecha_actual->month;

        $month = Month::select('month')->where('cod',$mes)->first();

        $resumen = DB::table('areas as a')
            ->select('cod_programa','cod_actividad','cod_item','planificado','area','ai.mes',DB::raw('IFNULL(extra, 0) extra'), DB::raw('IFNULL(planificado+extra, planificado) total'), 'devengado')
            ->join(DB::raw('(SELECT ai.id,cod_programa,cod_actividad,cod_item,ai.area_id,mes,sum(monto) planificado, sum(devengado) devengado FROM area_item ai INNER JOIN items i ON i.id=ai.item_id INNER JOIN carga_inicial ci ON (i.cod_programa=ci.programa and i.cod_actividad=ci.actividad and i.cod_item=ci.renglon) GROUP BY area_id,mes) ai'), function ($join) {
                $join->on('a.id', '=', 'ai.area_id');
            })
            ->leftJoin(DB::raw('(SELECT area_item_id,mes,area_id,sum(monto) extra from extras GROUP BY area_id) e'), function ($join) {
                $join->on('e.area_id', '=', 'ai.area_id');
                $join->on('e.mes', '=', 'ai.mes');
            })
//            ->where('ai.mes', 'like', '%' . $mes . '%')
            ->where('ai.mes', '=',$mes)
            ->get();

        $devengado_pacs = DB::table('area_item as ai')
            ->select('p.id', 'i.cod_item','i.cod_programa','i.cod_actividad', 'i.item', 'p.mes', 'p.presupuesto', 'p.disponible', 'p.comprometido', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')

//            ->where('ai.mes', 'like', '%' . $mes . '%')
            ->where('ai.mes', '=',$mes)
            ->get();

        $resumenArray=[];
        foreach ($resumen as $r){
            $resumenArray[] = [
                'labels' => $r->area,
                'eje' => round(($r->devengado-$r->extra)/($r->planificado)*100,2),
                'no_eje'=>round(100-(($r->devengado-$r->extra)/($r->planificado)*100),2),
            ];
        }

        return view('welcome',compact('resumenArray','month'));
    }

}
