<?php

namespace App\Http\Controllers;

use App\AreaItem;
use App\Esigef;
use App\Exercise;
use App\Extra;
use App\Month;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class HistoricoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**
     * Mostrar historico
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //en caso que se de en el boton de exportar y no en el de buscar en el formulario
        $exportar = $request->get('exportar', false);
        if ($exportar) {
            return $this->exportHistorico($request);
        }

        $ejercicios = Exercise::select(DB::raw('ejercicio,id'))->get();
        $list_ejercicios = $ejercicios->pluck('ejercicio', 'id');

        $ejercicio = $request->input('ejercicio'); //ejercicio seleccionado, exercise_id

        $fecha_actual = Carbon::now();
        //$month = $fecha_actual->formatLocalized('%B');
        $month = $fecha_actual->month; //1,2,3,...,12
        $year = $fecha_actual->year; //2018,2019,...

        $mes = $request->input('mes'); //mes esleccionado

        $meses = Month::select(DB::raw('month,cod'))->get();
        $list_meses = $meses->pluck('month', 'cod');

        if ($mes === null || $mes == "") {
            $mes = $month;
        }

        if ($ejercicio === null || $ejercicio == "") {
            $ej = Exercise::where('ejercicio', $year)->select('id')->first();
            $ejercicio = $ej->id;
        }

        //Historico guardado del devengado del esigef del año y mes seleccionado con los procesos del sistema del mismo mes
        $historico = Esigef::from('esigefs as eg')
            ->leftjoin('items as i', function ($join) { //items
                $join->on('eg.cod_programa', '=', 'i.cod_programa');
                $join->on('eg.cod_actividad', '=', 'i.cod_actividad');
                $join->on('eg.cod_item', '=', 'i.cod_item');
            })
            ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
            ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
            ->leftJoin(DB::raw(
                '(select area ingresoArea,extras.item_id ingresoItemID,extras.mes,sum(extras.monto) ingresoExtra from extras 
                        inner join months m on m.cod=extras.mes
                        inner join areas a on a.id=extras.area_id
                        where extras.mes=' . $mes . '
                        group by extras.item_id, extras.mes) e'
            ), function ($join) {
                $join->on('e.ingresoItemID', '=', 'i.id');
            })
            ->leftJoin(DB::raw(
                '(select area_item.item_id aiItemID, area_item.area_id aiAreaID,area_item.mes aiMes,sum(area_item.monto) aiMonto,a.area aiArea from area_item 
                        inner join months m on m.cod=area_item.mes
                         inner join areas a on a.id=area_item.area_id
                         where area_item.mes=' . $mes . '
                        group by area_item.item_id,area_item.mes,area_item.area_id) ai'
            ), function ($join) {
                $join->on('aiItemID', '=', 'i.id');
            })
            ->select('eg.exercise_id', 'eg.mes', 'eg.cod_programa', 'eg.cod_actividad', 'eg.cod_item', 'eg.codificado', 'eg.devengado', 'i.id as itemID', 'i.item', 'i.presupuesto as itemPresupuesto', 'prog.programa', 'act.actividad', 'ingresoArea', 'ingresoItemID', DB::raw('IFNULL(ingresoExtra, 0) ingresoExtra'), 'aiItemID', 'aiMes', 'ai.aiArea', 'ai.aiAreaID', DB::raw('IFNULL(aiMonto, 0) aiMonto'))
            ->where([
                ['eg.exercise_id', $ejercicio],
                ['eg.mes', $mes]
            ])
//                ->where('aiMes', $mes)
//                 ->take(20)
//                ->orderBy('i.id', 'desc')
            ->get();

        $view = view('historico.index', compact('mes', 'historico', 'ejercicio', 'list_meses', 'list_ejercicios'));
        if ($request->ajax()) {
            $sections = $view->rendersections();
            return response()->json($sections['content']);
        } else return $view;
    }

    /**
     * Cargar vista para hacer cierre mensual (Configuracion/Cierre)
     *
     * @param Request $request
     */
    public function cierre(Request $request)
    {
        if (Auth::user()->can('hacer-cierre')) {

            $fecha_actual = Carbon::now();
//            $month = $fecha_actual->formatLocalized('%B');
            $month = $fecha_actual->month;

            $meses = Month::select(DB::raw('month,cod'))->get();
            $list_meses = $meses->pluck('month', 'cod');

            $mes = $request->input('mes');//cod del mes seleccionado

            if (empty($mes)) {
                $mes_actual = Month::where('cod', $month)->first();
                $mes = $mes_actual->cod;
            }

            $mes_select = Month::where('cod', $mes)->first();

            //Esigef cargado con los items planificados inicialmente
            $esigef_items = DB::table('carga_inicial as ci')
                ->leftjoin('items as i', function ($join) { //items
                    $join->on('ci.programa', '=', 'i.cod_programa');
                    $join->on('ci.actividad', '=', 'i.cod_actividad');
                    $join->on('ci.renglon', '=', 'i.cod_item');
                })
                ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
                ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
                ->select('ci.ejercicio', 'ci.programa as esigefPrograma', 'ci.actividad as esigefActividad', 'ci.renglon as esigefItem', 'ci.codificado as esigefCodificado', 'ci.devengado as esigefDevengado', 'i.id as itemID', 'i.cod_programa', 'prog.programa', 'i.cod_actividad', 'act.actividad', 'i.cod_item', 'grupo_gasto', 'i.item', DB::raw('IFNULL (i.presupuesto,0) itemPresupuesto'), DB::raw('IFNULL(i.disponible,0) itemDisponible'))
//                ->orderBy('i.id', 'asc')
//                ->take(10)
                ->get();

            $view = view('historico.cierre_mensual', compact('mes', 'esigef_items', 'meses', 'list_meses', 'mes_select'));
            if ($request->ajax()) {
                $sections = $view->rendersections();
                return response()->json($sections['content']);
            } else return $view;
        } else return abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Guardar cierre en la tabla esigef, del esigef cargado
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('hacer-cierre')) {

            if ($request->input('mes') == 'placeholder') {
                return redirect()->route('admin.historico.cierre')->withInput()->with('message_danger', 'Seleccione el mes para el cierre');
            }

            $fecha_actual = Carbon::now();
            $year = $fecha_actual->year; //2018

            $ejercicio = Exercise::where('ejercicio', $year)->first(); //(id=1, ejercicio=2018)

            $mes_cod = $request->input('mes'); //2, 3, 4, ....
            $mes_select = Month::where('cod', $mes_cod)->first();//mes seleccionado

            if (isset($mes_select)) {
                $mes = $mes_select->month; //Febrero, Marzo, etc
            }

            $historico = Esigef::where('mes', $mes_cod)->where('exercise_id', $ejercicio->id)->first();

            //ya existe historico de ese mes guardado preguntra por actualizacion
            if (isset($historico)) {
                return response()->json([
                    "response" => 'Ya existe un cierre del mes ' . $mes . ' para el ejercicio ' . $ejercicio->ejercicio . '. Desea actualizar la información ?',
                    "tipo" => "existe"
                ]);
            }

            //encaso de que no exita el mes guardao en el historico continuar

            //Esigef cargado con los items planificados inicialmente para cierre mensual del dev de esigef
            $esigef_items = DB::table('carga_inicial as ci')
                ->leftjoin('items as i', function ($join) { //items
                    $join->on('ci.programa', '=', 'i.cod_programa');
                    $join->on('ci.actividad', '=', 'i.cod_actividad');
                    $join->on('ci.renglon', '=', 'i.cod_item');
                })
                ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
                ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
                ->select('ci.ejercicio', 'ci.programa as esigefPrograma', 'ci.actividad as esigefActividad', 'ci.renglon as esigefItem', 'ci.codificado as esigefCodificado', 'ci.devengado as esigefDevengado', 'i.id as item_id', 'i.cod_programa', 'prog.programa', 'i.cod_actividad', 'act.actividad', 'i.cod_item', 'grupo_gasto', 'i.item', DB::raw('IFNULL (i.presupuesto,0) itemPresupuesto'), DB::raw('IFNULL(i.disponible,0) itemDisponible'))
                ->where('ci.ejercicio', $year)
                ->get();

            if (count($esigef_items) <= 0) {
                return response()->json([
                    "response" => 'No existen datos para guardar en el mes ' . $mes,
                    "tipo" => "error"
                ]);
            }

            $insert = [];
            try {
                DB::beginTransaction();
                foreach ($esigef_items as $key => $value) {
                    $insert[] = [
                        "exercise_id" => $ejercicio->id,
                        "cod_programa" => $value->esigefPrograma,
                        "cod_actividad" => $value->esigefActividad,
                        "cod_item" => $value->esigefItem,
                        "codificado" => $value->esigefCodificado,
                        "devengado" => $value->esigefDevengado,
                        "mes" => $mes_cod
                    ];
                }

                if (!empty($insert)) {
                    DB::table('esigefs')->insert($insert);
                }

                DB::commit();
                return response()->json([
                    "response" => 'Se realizo el cierre correspondiente al mes de ' . $mes . ' correctamente.',
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    "response" => "Ha ocurrido un error, no se pudo realizar el cierre",
//                       "response" => $e->getMessage(),
                    "tipo" => "error"
                ]);
            }

        } else return abort(403);
    }

    /**
     * Actualizar historico existente
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function actualizarHistorico(Request $request)
    {
        $fecha_actual = Carbon::now();
        $year = $fecha_actual->year; //2018
        $ejercicio = Exercise::where('ejercicio', $year)->first(); //(id=1, ejercicio=2018)
        $mes_cod = $request->input('mes'); //2, 3, 4, ....
        $mes_select = Month::where('cod', $mes_cod)->first();//mes seleccionado
        if (isset($mes_select)) {
            $mes = $mes_select->month; //Febrero, Marzo, etc
        }

        try {
            DB::beginTransaction();

            $historico_mes = Esigef::where('mes', $mes_cod)->where('exercise_id', $ejercicio->id)->get()->toArray();

            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $historico_mes);

            DB::table('esigefs')->whereIn('id', $ids_to_delete)->delete();

            $esigef_items = DB::table('carga_inicial as ci')
                ->leftjoin('items as i', function ($join) { //items
                    $join->on('ci.programa', '=', 'i.cod_programa');
                    $join->on('ci.actividad', '=', 'i.cod_actividad');
                    $join->on('ci.renglon', '=', 'i.cod_item');
                })
                ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
                ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
                ->select('ci.ejercicio', 'ci.programa as esigefPrograma', 'ci.actividad as esigefActividad', 'ci.renglon as esigefItem', 'ci.codificado as esigefCodificado', 'ci.devengado as esigefDevengado', 'i.id as item_id', 'i.cod_programa', 'prog.programa', 'i.cod_actividad', 'act.actividad', 'i.cod_item', 'grupo_gasto', 'i.item', DB::raw('IFNULL (i.presupuesto,0) itemPresupuesto'), DB::raw('IFNULL(i.disponible,0) itemDisponible'))
                ->where('ci.ejercicio', $year)
                ->get();

            if (count($esigef_items) <= 0) {
                return response()->json([
                    "response" => 'No existen datos para guardar en el mes ' . $mes,
                    "tipo" => "error"
                ]);
            }

            $insert = [];

            foreach ($esigef_items as $key => $value) {
                $insert[] = [
                    "exercise_id" => $ejercicio->id,
                    "cod_programa" => $value->esigefPrograma,
                    "cod_actividad" => $value->esigefActividad,
                    "cod_item" => $value->esigefItem,
                    "codificado" => $value->esigefCodificado,
                    "devengado" => $value->esigefDevengado,
                    "mes" => $mes_cod
                ];
            }

            if (!empty($insert)) {
                DB::table('esigefs')->insert($insert);
            }

            DB::commit();

            return response()->json([
                "response" => 'Se actualizo el cierre correspondiente al mes de ' . $mes . ' correctamente',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "response" => "Ha ocurrido un error, no se pudo realizar el cierre",
//                       "response" => $e->getMessage(),
                "tipo" => "error"
            ]);
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
    public
    function edit($id)
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
    public
    function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        //
    }

    /**
     * Exportar para Informe Excell
     *
     * @param Request $request
     */

    public function exportHistorico(Request $request)
    {
//        if (Auth::user()->can('hacer-cierre')) {

        $mes_cod = $request->input('mes');
        $ejercicio = $request->input('ejercicio'); //ejercicio_id

        if ($ejercicio === null || $ejercicio == "") {
            return back()->withInput()->with('message_danger', 'Debe seleccionar el ejercicio');
        }

        if ($mes_cod != null) {//historico de un solo mes

            $historico_mes = Esigef::from('esigefs as eg')
                ->leftjoin('items as i', function ($join) { //items
                    $join->on('eg.cod_programa', '=', 'i.cod_programa');
                    $join->on('eg.cod_actividad', '=', 'i.cod_actividad');
                    $join->on('eg.cod_item', '=', 'i.cod_item');
                })
                ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
                ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
                ->leftJoin(DB::raw(
                    '(select area ingresoArea,extras.item_id ingresoItemID,extras.mes,sum(extras.monto) ingresoExtra from extras 
                        inner join months m on m.cod=extras.mes
                        inner join areas a on a.id=extras.area_id
                        where extras.mes=' . $mes_cod . '
                        group by extras.item_id, extras.mes) e'
                ), function ($join) {
                    $join->on('e.ingresoItemID', '=', 'i.id');
                })
                ->leftJoin(DB::raw(
                    '(select area_item.item_id aiItemID, area_item.area_id aiAreaID,area_item.mes aiMes,sum(area_item.monto) aiMonto,a.area aiArea from area_item 
                        inner join months m on m.cod=area_item.mes
                         inner join areas a on a.id=area_item.area_id
                         where area_item.mes=' . $mes_cod . '
                        group by area_item.item_id,area_item.mes,area_item.area_id) ai'
                ), function ($join) {
                    $join->on('aiItemID', '=', 'i.id');
                })
                ->leftJoin(DB::raw( //agregar a cada item el nombre del area tomandolo de area_item o de extras
                    '(SELECT a.area Areas,i.cod_programa,i.cod_actividad,i.cod_item FROM items i 
                        left join area_item ai on ai.item_id=i.id
                        left join extras e on e.item_id=i.id
                        left join areas a on (ai.area_id=a.id or e.area_id=a.id)
                        group by i.id) a'
                ), function ($join) {
                    $join->on('a.cod_programa', '=', 'i.cod_programa');
                    $join->on('a.cod_actividad', '=', 'i.cod_actividad');
                    $join->on('a.cod_item', '=', 'i.cod_item');
                })
                ->select('eg.exercise_id', 'eg.mes', 'eg.cod_programa', 'eg.cod_actividad', 'eg.cod_item', 'eg.codificado', 'eg.devengado', 'i.id as itemID', 'i.item', 'i.presupuesto as itemPresupuesto', 'prog.programa', 'act.actividad', 'ingresoArea', 'ingresoItemID', DB::raw('IFNULL(ingresoExtra, 0) ingresoExtra'), 'aiItemID', 'aiMes', 'ai.aiArea', 'ai.aiAreaID', DB::raw('IFNULL(aiMonto, 0) aiMonto'), 'a.Areas')
                ->where([
                    ['eg.exercise_id', $ejercicio],
                    ['eg.mes', $mes_cod]
                ])
                ->get();

            $meses = Month::where('cod', $mes_cod)->first();
            $encabezado = [
                'mes' => $meses->month,
            ];

            $hist_mes_array[] = ['Responsable', 'Programa', 'Actividad', 'Item', 'Nombre del Item', 'Planificado', 'Devengado', 'Diferencia'];
            foreach ($historico_mes as $hm) {
                $dev = $hm->devengado - $hm->ingresoExtra;
                $dif = (float)$hm->aiMonto - $dev;
                $hist_mes_array[] = [
                    'resp' => $hm->Areas,
                    'cod_prog' => $hm->cod_programa,
                    'cod_act' => $hm->cod_actividad,
                    'cod_it' => $hm->cod_item,
                    'item' => $hm->item,
                    'plan' => (float)$hm->aiMonto,
                    'devengado' => (float)$dev,
                    'diferencia' =>  $dif
                ];
            }

            Excel::create('Historico Control Poa ' . $meses->month . ' - ' . Carbon::now() . '', function ($excel) use ($hist_mes_array, $encabezado) {//crear excel pasando array al closure

                $excel->sheet('POA - ' . $encabezado['mes'] . '', function ($sheet) use ($hist_mes_array, $encabezado) {

                    $sheet->row(1, ["Mes: " . $encabezado['mes']]);
                    $sheet->mergeCells('A1:E1');
                    $sheet->cells('A1:E2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        //$cells->setBackground('#B2B2B2');
                        $cells->setBackground('#404040');
                        $cells->setFontWeight('bold');
                        //alineacion horizontal
                        $cells->setAlignment('center');
                        // alineacion vertical
                        $cells->setValignment('center');
                        // tipo de letra
                        $cells->setFontFamily('Arial');
                        // tamaño de letra
                        $cells->setFontSize(11);
                        // bordes (top, right, bottom, left)
//                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                    });
                    //planificado
                    $sheet->cells('F1:F2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#4472C4');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });
                    //devengado
                    $sheet->cells('G1:G2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#00B050');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });
                    //diferencia
                    $sheet->cells('H1:H2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#ED9131');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });

                    // Set top, right, bottom, left margins
                    $sheet->setPageMargin(array(
                        0.25, 0.30, 0.25, 0.30
                    ));

                    // Set font with ->setStyle()`
                    $sheet->setStyle([
                        // Font family
                        $sheet->setFontFamily('Arial'),
                        // Font size
                        $sheet->setFontSize(12),
                        // Font bold
                        $sheet->setFontBold(false),
                        // Sets all borders
                        $sheet->setAllBorders('thin'),
                    ]);

                    // freeze fila
                    $sheet->setFreeze('F3');

                    $sheet->setColumnFormat([
                        'F' => '#,##0.00',
                        'G' => '#,##0.00',
                        'H' => '#,##0.00',
                    ]);

                    $sheet->setAutoFilter('A2:E2');

                    $sheet->fromArray($hist_mes_array, null, 'A2', false, false);

                });
            })->export('xlsx');

        } else {//historico completo

            $historico_mes = Esigef::from('esigefs as eg')
                ->leftjoin('items as i', function ($join) { //items
                    $join->on('eg.cod_programa', '=', 'i.cod_programa');
                    $join->on('eg.cod_actividad', '=', 'i.cod_actividad');
                    $join->on('eg.cod_item', '=', 'i.cod_item');
                })
                ->leftJoin('actividads as act', 'act.cod_actividad', '=', 'i.cod_actividad')
                ->leftJoin('programas as prog', 'prog.cod_programa', '=', 'i.cod_programa')
                ->leftJoin(DB::raw(
                    '(select area ingresoArea,extras.area_id,extras.item_id ingresoItemID,sum(extras.monto) ingresoExtra from extras 
                        inner join months m on m.cod=extras.mes
                        inner join areas a on a.id=extras.area_id
                        group by extras.item_id,area_id) e'
                ), function ($join) {
                    $join->on('e.ingresoItemID', '=', 'i.id');
                })
                ->leftJoin(DB::raw(
                    '(select area_item.item_id aiItemID, area_item.area_id aiAreaID,area_item.mes aiMes,sum(area_item.monto) aiMonto,a.area aiArea                          from area_item 
                        inner join months m on m.cod=area_item.mes
                         inner join areas a on a.id=area_item.area_id
                        group by area_item.item_id,area_item.area_id) ai'
                ), function ($join) {
                    $join->on('aiItemID', '=', 'i.id');
                })
                ->leftJoin(DB::raw( //agregar a cada item el nombre del area tomandolo de area_item o de extras
                    '(SELECT a.area Areas,i.cod_programa,i.cod_actividad,i.cod_item FROM items i 
                        left join area_item ai on ai.item_id=i.id
                        left join extras e on e.item_id=i.id
                        left join areas a on (ai.area_id=a.id or e.area_id=a.id)
                        group by i.id) a'
                ), function ($join) {
                    $join->on('a.cod_programa', '=', 'i.cod_programa');
                    $join->on('a.cod_actividad', '=', 'i.cod_actividad');
                    $join->on('a.cod_item', '=', 'i.cod_item');
                })
                ->select('eg.exercise_id', 'eg.cod_programa', 'eg.cod_actividad', 'eg.cod_item', 'i.id as itemID', 'i.item', 'i.presupuesto as itemPresupuesto', 'prog.programa', 'act.actividad', 'ingresoArea', 'ingresoItemID', DB::raw('IFNULL(ingresoExtra, 0) ingresoExtra'), 'aiItemID', 'ai.aiArea', 'ai.aiAreaID', DB::raw('IFNULL(aiMonto, 0) aiMonto'), 'a.Areas')
                ->where('eg.exercise_id', $ejercicio)
                //->where('i.id', 103) para comprobar los resultados devueltos
                ->groupBy('cod_programa', 'cod_actividad', 'cod_item', 'item')
//                ->take(20)
//                ->orderBy('i.id', 'asc')
                ->get();

            //poafdg
            $area_item = AreaItem::from('area_item as ai')
                ->leftjoin('items as i', 'i.id', '=', 'ai.item_id')
                ->select('ai.item_id', 'ai.monto', 'ai.mes', 'ai.area_id')
                ->get()->toArray();

            //historico guardado
            $esigef = Esigef::from('esigefs as eg')
                ->select('eg.cod_programa', 'eg.cod_actividad', 'eg.cod_item', 'eg.mes', 'eg.devengado')
                ->where('exercise_id', $ejercicio)
                ->get()->toArray();

            //extras
            $extras = Extra::from('extras as e')
                ->select('e.item_id', 'e.area_id', 'e.mes', DB::raw('sum(e.monto) as monto'))
                ->groupBy('e.item_id', 'e.mes')
                ->get()->toArray();

//            $historico_mes1 = Esigef::
//            select('id', 'area', 'cod_programa', 'cod_actividad', 'cod_item', 'item', DB::raw('
//                        CASE WHEN mes = \'1\' THEN planificado ELSE 0 END AS p_ene,
//                        CASE WHEN mes = \'2\' THEN planificado ELSE 0 END AS p_feb,
//                        CASE WHEN mes = \'3\' THEN planificado ELSE 0 END AS p_mar,
//                        CASE WHEN mes = \'4\' THEN planificado ELSE 0 END AS p_abr,
//                        CASE WHEN mes = \'5\' THEN planificado ELSE 0 END AS p_may,
//                        CASE WHEN mes = \'6\' THEN planificado ELSE 0 END AS p_jun,
//                        CASE WHEN mes = \'7\' THEN planificado ELSE 0 END AS p_jul,
//                        CASE WHEN mes = \'8\' THEN planificado ELSE 0 END AS p_ago,
//                        CASE WHEN mes = \'9\' THEN planificado ELSE 0 END AS p_sep,
//                        CASE WHEN mes = \'10\' THEN planificado ELSE 0 END AS p_oct,
//                        CASE WHEN mes = \'11\' THEN planificado ELSE 0 END AS p_nov,
//                        CASE WHEN mes = \'12\' THEN planificado ELSE 0 END AS p_dic,
//                        CASE WHEN mes = \'1\' THEN devengado-extras ELSE 0 END AS dev_ene,
//                        CASE WHEN mes = \'2\' THEN devengado-extras ELSE 0 END AS dev_feb,
//                        CASE WHEN mes = \'3\' THEN devengado-extras ELSE 0 END AS dev_mar,
//                        CASE WHEN mes = \'4\' THEN devengado-extras ELSE 0 END AS dev_abr,
//                        CASE WHEN mes = \'5\' THEN devengado-extras ELSE 0 END AS dev_may,
//                        CASE WHEN mes = \'6\' THEN devengado-extras ELSE 0 END AS dev_jun,
//                        CASE WHEN mes = \'7\' THEN devengado-extras ELSE 0 END AS dev_jul,
//                        CASE WHEN mes = \'8\' THEN devengado-extras ELSE 0 END AS dev_ago,
//                        CASE WHEN mes = \'9\' THEN devengado-extras ELSE 0 END AS dev_sep,
//                        CASE WHEN mes = \'10\' THEN devengado-extras ELSE 0 END AS dev_oct,
//                        CASE WHEN mes = \'11\' THEN devengado-extras ELSE 0 END AS dev_nov,
//                        CASE WHEN mes = \'12\' THEN planificado-(devengado-extras) ELSE 0 END AS dev_dic,
//                        CASE WHEN mes = \'1\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_ene,
//                        CASE WHEN mes = \'2\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_feb,
//                        CASE WHEN mes = \'3\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_mar,
//                        CASE WHEN mes = \'4\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_abr,
//                        CASE WHEN mes = \'5\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_may,
//                        CASE WHEN mes = \'6\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_jun,
//                        CASE WHEN mes = \'7\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_jul,
//                        CASE WHEN mes = \'8\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_ago,
//                        CASE WHEN mes = \'9\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_sep,
//                        CASE WHEN mes = \'10\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_oct,
//                        CASE WHEN mes = \'11\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_nov,
//                        CASE WHEN mes = \'12\' THEN planificado-(devengado-extras) ELSE 0 END AS dif_dic
//
//                '))
//                ->where('exercise_id', $ejercicio)
//                ->groupBy('id', 'area', 'cod_programa', 'cod_actividad', 'cod_item', 'item')
//                ->get();


            $hist_array[] = ['RESPONSABLE', 'PROGRAMA', 'ACTIVIDAD', 'ITEM', 'NOMBRE DEL ITEM', 'VALOR ANUAL', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

            foreach ($historico_mes as $hm) {
                //planificado area_item poafdg
                $p_ene = $p_feb = $p_mar = $p_abr = $p_may = $p_jun = $p_jul = $p_ago = $p_sep = $p_oct = $p_nov = $p_dic = 0;
                foreach ($area_item as $value) {
                    if ($hm->itemID == $value['item_id']) {
                        switch ($value['mes']) {
                            case 1:
                                $p_ene = $value['monto'];
                                break;
                            case 2:
                                $p_feb = $value['monto'];
                                break;
                            case 3:
                                $p_mar = $value['monto'];
                                break;
                            case 4:
                                $p_abr = $value['monto'];
                                break;
                            case 5:
                                $p_may = $value['monto'];
                                break;
                            case 6:
                                $p_jun = $value['monto'];
                                break;
                            case 7:
                                $p_jul = $value['monto'];
                                break;
                            case 8:
                                $p_ago = $value['monto'];
                                break;
                            case 9:
                                $p_sep = $value['monto'];
                                break;
                            case 10:
                                $p_oct = $value['monto'];
                                break;
                            case 11:
                                $p_nov = $value['monto'];
                                break;
                            case 12:
                                $p_dic = $value['monto'];
                                break;
                        }
                    }
                }
                //devengado de esigef guardado
                $dev_ene = $dev_feb = $dev_mar = $dev_abr = $dev_may = $dev_jun = $dev_jul = $dev_ago = $dev_sep = $dev_oct = $dev_nov = $dev_dic = 0;
                foreach ($esigef as $value) {
                    if ($hm->cod_programa == $value['cod_programa'] && $hm->cod_actividad == $value['cod_actividad'] && $hm->cod_item == $value['cod_item']) {
                        switch ($value['mes']) {
                            case 1:
                                $dev_ene = $value['devengado'];
                                break;
                            case 2:
                                $dev_feb = $value['devengado'];
                                break;
                            case 3:
                                $dev_mar = $value['devengado'];
                                break;
                            case 4:
                                $dev_abr = $value['devengado'];
                                break;
                            case 5:
                                $dev_may = $value['devengado'];
                                break;
                            case 6:
                                $dev_jun = $value['devengado'];
                                break;
                            case 7:
                                $dev_jul = $value['devengado'];
                                break;
                            case 8:
                                $dev_ago = $value['devengado'];
                                break;
                            case 9:
                                $dev_sep = $value['devengado'];
                                break;
                            case 10:
                                $dev_oct = $value['devengado'];
                                break;
                            case 11:
                                $dev_nov = $value['devengado'];
                                break;
                            case 12:
                                $dev_dic = $value['devengado'];
                                break;
                        }
                    }
                }

                //extras
                $e_ene = $e_feb = $e_mar = $e_abr = $e_may = $e_jun = $e_jul = $e_ago = $e_sep = $e_oct = $e_nov = $e_dic = 0;
                foreach ($extras as $value) {
                    if ($hm->itemID == $value['item_id']) {
                        switch ($value['mes']) {
                            case 1:
                                $e_ene = $value['monto'];
                                break;
                            case 2:
                                $e_feb = $value['monto'];
                                break;
                            case 3:
                                $e_mar = $value['monto'];
                                break;
                            case 4:
                                $e_abr = $value['monto'];
                                break;
                            case 5:
                                $e_may = $value['monto'];
                                break;
                            case 6:
                                $e_jun = $value['monto'];
                                break;
                            case 7:
                                $e_jul = $value['monto'];
                                break;
                            case 8:
                                $e_ago = $value['monto'];
                                break;
                            case 9:
                                $e_sep = $value['monto'];
                                break;
                            case 10:
                                $e_oct = $value['monto'];
                                break;
                            case 11:
                                $e_nov = $value['monto'];
                                break;
                            case 12:
                                $e_dic = $value['monto'];
                                break;
                        }
                    }
                }

                $hist_array[] = [
                    'resp' => $hm->Areas,
                    'cod_prog' => $hm->cod_programa,
                    'cod_act' => $hm->cod_actividad,
                    'cod_it' => $hm->cod_item,
                    'item' => $hm->item,
                    'val_anual' => $hm->aiMonto,
                    'p_ene' =>  (float)$p_ene,
                    'p_feb' =>  (float)$p_feb,
                    'p_mar' =>  (float)$p_mar,
                    'p_abr' =>  (float)$p_abr,
                    'p_may' =>  (float)$p_may,
                    'p_jun' =>  (float)$p_jun,
                    'p_jul' =>  (float)$p_jul,
                    'p_ago' =>  (float)$p_ago,
                    'p_sep' =>  (float)$p_sep,
                    'p_oct' =>  (float)$p_oct,
                    'p_nov' =>  (float)$p_nov,
                    'p_dic' =>  (float)$p_dic,
                    'dev_ene' =>  (float)$dev_ene - $e_ene,
                    'dev_feb' =>  (float)$dev_feb - $e_feb,
                    'dev_mar' => (float)$dev_mar - $e_mar,
                    'dev_abr' =>  (float)$dev_abr - $e_abr,
                    'dev_may' =>  (float)$dev_may - $e_may,
                    'dev_jun' =>  (float)$dev_jun - $e_jun,
                    'dev_jul' =>  (float)$dev_jul - $e_jul,
                    'dev_ago' =>  (float)$dev_ago - $e_ago,
                    'dev_sep' =>  (float)$dev_sep - $e_sep,
                    'dev_oct' =>  (float)$dev_oct - $e_oct,
                    'dev_nov' => (float)$dev_nov - $e_nov,
                    'dev_dic' =>  (float)$dev_dic - $e_dic,
                    'dif_ene' =>  (float)$p_ene - ($dev_ene - $e_ene),
                    'dif_feb' =>  (float)$p_feb - ($dev_feb - $e_feb),
                    'dif_mar' =>  (float)$p_mar - ($dev_mar - $e_mar),
                    'dif_abr' =>  (float)$p_abr - ($dev_abr - $e_abr),
                    'dif_may' =>  (float)$p_may - ($dev_may - $e_may),
                    'dif_jun' =>  (float)$p_jun - ($dev_jun - $e_jun),
                    'dif_jul' =>  (float)$p_jul - ($dev_jul - $e_jul),
                    'dif_ago' =>  (float)$p_ago - ($dev_ago - $e_ago),
                    'dif_sep' =>  (float)$p_sep - ($dev_sep - $e_sep),
                    'dif_oct' =>  (float)$p_oct - ($dev_oct - $e_oct),
                    'dif_nov' =>  (float)$p_nov - ($dev_nov - $e_nov),
                    'dif_dic' =>  (float)$p_dic - ($dev_dic - $e_dic),
                ];
            }

            Excel::create('Historico ' . $ejercicio . ' - ' . Carbon::now() . '', function ($excel) use ($hist_array) {

                $excel->sheet('Control POA', function ($sheet) use ($hist_array) {//crear la hoja pasando array al closure

                    $sheet->setOrientation('landscape');

                    $sheet->cells('A1:F1', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#404040');
                        $cells->setFontWeight('bold');
                        //alineacion horizontal
                        $cells->setAlignment('left');
                        // alineacion vertical
                        $cells->setValignment('center');
                        // tipo de letra
                        $cells->setFontFamily('Arial');
                        // tamaño de letra
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A2:F2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#404040');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('left');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });

                    $sheet->setCellValue('G1', 'PLANIFICADO MENSUAL');
                    $sheet->mergeCells('G1:R1');
                    $sheet->cells('G1:R1', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#4472C4');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });
                    $sheet->cells('G2:R2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#4472C4');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });

                    $sheet->mergeCells('S1:AD1');
                    $sheet->setCellValue('S1', 'DEVENGADO ESIGEF');
                    $sheet->cells('S1:AD1', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#00B050');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });
                    $sheet->cells('S2:AD2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#00B050');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });

                    $sheet->mergeCells('AE1:AP1');
                    $sheet->setCellValue('AE1', 'DIFERENCIAS PLANIFICADO VS DEVENGADO');
                    $sheet->cells('AE1:AP1', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#ED9131');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });
                    $sheet->cells('AE2:AP2', function ($cells) {
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground('#ED9131');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFontFamily('Arial');
                        $cells->setFontSize(11);
                    });

                    // Set top, right, bottom, left margins
                    $sheet->setPageMargin(array(
                        0.25, 0.30, 0.25, 0.30
                    ));

                    // Set font with ->setStyle()
                    $sheet->setStyle([
                        // Font family
                        $sheet->setFontFamily('Arial'),
                        // Font size
                        $sheet->setFontSize(12),
                        // Font bold
                        $sheet->setFontBold(false),

                    ]);

                    // freeze fila
                    $sheet->setFreeze('F3');

                    $sheet->setAutoFilter('A2:E2');

                    // Set multiple column formats
                    $sheet->setColumnFormat(array(
                        'F:AP' => '#,##0.00'
                    ));

                    //crear la hoja a partir del array
                    //5to parametro false pasa como encabesado de la primera fila los nombres de las columnas
                    $sheet->fromArray($hist_array, null, 'A2', false, false);

                });
            })->export('xlsx');

        }

//        }else return abort(403);

    }
}
