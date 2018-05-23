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
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $fecha_actual=Carbon::now();
        $mes=$fecha_actual->month;

        $month = Month::select('month')->where('cod',$mes)->first();

        //muestra resumen del mes actual solamente con lo cargado de esigef
        $resumen = DB::table('areas as a')
            ->join(DB::raw('
                (SELECT ai.id,ai.area_id,ai.item_id,mes,sum(monto) planificado, sum(devengado) devengado FROM area_item ai 
                INNER JOIN items i ON i.id=ai.item_id 
                INNER JOIN carga_inicial ci ON 
                (i.cod_programa=ci.programa and i.cod_actividad=ci.actividad and i.cod_item=ci.renglon) 
                GROUP BY area_id,mes) 
                ai'
            ), function ($join) {
                $join->on('a.id', '=', 'ai.area_id');
            })
            ->leftJoin(DB::raw('(SELECT e.area_id,e.mes,e.item_id,sum(e.monto) extra from areas a 
                                    inner join extras as e on e.area_id=a.id 
	                                group by area_id,mes) 
	                                e'), function ($join) {
                $join->on('e.area_id', '=', 'a.id');
                $join->on('e.mes', '=', 'ai.mes');
            })
            ->select('planificado','devengado', 'area', 'ai.mes', DB::raw('IFNULL(extra, 0) extra'), DB::raw('IFNULL(planificado+extra, planificado) total'),'ai.area_id','e.mes')
            ->where('ai.mes', '=', $mes)
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
