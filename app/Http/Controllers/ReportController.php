<?php

namespace App\Http\Controllers;

use App\Apertura;
use App\Informe;
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
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**Resumen por mes y area
     * @param Request $request
     */
    public function resumenMensual(Request $request)
    {
        if (Auth::user()->can('consultor')) {

            $fecha_actual = Carbon::now();
            $month = $fecha_actual->month; //mes de la fecha actual : 1,2,3,4,....,12

            $meses = Month::select(DB::raw('month,cod'))->get();
            $list_meses = $meses->pluck('month', 'cod');

            $mes_cod = $request->input('mes'); //mes seleccionado de la lista
            $nombre_mes = Month::select('month')->where('cod', $mes_cod)->first();//mes eleccionado

            if (is_null($nombre_mes)) {
                $mes_actual = Month::where('cod', $month)->first();//mes actual
                $mes = $mes_actual->month;
            } else {
                $mes = $nombre_mes->month;
            }

//             DB::select( DB::raw("SELECT * FROM some_table WHERE some_col = '$someVariable'")
//        DB::table('users')
//            ->select('first_name', 'TotalCatches.*')
//            ->join(DB::raw('(SELECT user_id, COUNT(user_id) TotalCatch, DATEDIFF(NOW(), MIN(created_at)) Days, COUNT(user_id)/DATEDIFF(NOW(), MIN(created_at)) CatchesPerDay FROM `catch-text` GROUP BY user_id) TotalCatches'), function($join)
//            {
//                $join->on('users.id', '=', 'TotalCatches.user_id');
//            })
//            ->orderBy('TotalCatches.CatchesPerDay', 'DESC')
//            ->get();

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
                ->select('planificado', 'devengado', 'area', 'ai.mes', DB::raw('IFNULL(extra, 0) extra'), DB::raw('IFNULL(planificado+extra, planificado) total'), 'ai.area_id', 'e.mes')
                ->where('ai.mes', '=', $month)
                ->get();

            //procesos
            $devengado_pacs = DB::table('area_item as ai')
                ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
                ->join('items as i', 'ai.item_id', '=', 'i.id')
                ->join('areas as a', 'a.id', '=', 'ai.area_id')
                ->join('workers as w', 'w.id', '=', 'p.worker_id')
                ->join('departamentos as d', 'd.id', '=', 'w.departamento_id')
                ->select('p.id', 'i.cod_item', 'i.cod_programa', 'i.cod_actividad', 'i.item', 'p.mes', 'p.presupuesto', 'p.disponible', 'p.comprometido', 'p.devengado', 'w.nombres', 'w.apellidos', 'a.area', 'p.procedimiento', 'p.concepto', 'd.area_id as area_trabajador')
//            ->where('ai.mes', 'like', '%' . $mes . '%')
                ->where('ai.mes', '=', $mes_cod)
                ->get();

            $view = view('reportes.resumen_mensual', compact('mes', 'mes_cod', 'list_meses', 'resumen', 'devengado_pacs'));
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
        $reforma = DB::table('reformas as r')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto', 'ai.area_id')
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

//            PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        $pdf = PDF::loadView('reportes.reforma-pdf', compact('reforma', 'detalles_o', 'detalles_d', 'jefe_area'));
        //        return $pdf->download('Refroma.pdf');//descarga el pdf
        return $pdf->setPaper('letter', 'landscape')->stream('Reforma');//imprime en pantalla

    }

    /**
     * imprimir las reformas seleccionadas en pdf
     *
     * Este metodo no se esta utilizando solo las primeras lineas para llamar a otros metodos
     * @param $id
     * @return mixed
     */
    public function reformaSelectPDF(Request $request)
    {
        //en caso que se de en el boton de exportar en excell y no el de pdf redireccionar a la funcion de exportar en excel
        $exportar_excel = $request->get('imp_all_excel', false);
        if ($exportar_excel) {
            return $this->reformasAllExcel($request);
        }

        //en caso que se de en el boton de generar informe redireccionar a la funcion generar_informe
        $generar_informe = $request->get('gen_informe', false);
        if ($generar_informe) {
            return $this->generarInformeT($request);
        }


        $reformas_id = $request->input('imp_reformas');//arreglo con los id de refromas

        if (empty($reformas_id) || !isset($reformas_id)) {
            return redirect()->back()->with('message_danger', 'Debe seleccionar al menos una reforma para poder imprmir');
        }

        $reforma = DB::table('reformas as r')
            ->join('area_item as ai', 'ai.id', '=', 'r.area_item_id')
            ->join('items as i', 'i.id', '=', 'ai.item_id')
            ->join('months as m', 'm.cod', '=', 'ai.mes')
            ->join('reform_type as rt', 'rt.id', '=', 'r.reform_type_id')
            ->join('actividad_programa as ap', 'ap.id', '=', 'i.actividad_programa_id')
            ->join('programas as p', 'p.id', '=', 'ap.programa_id')
            ->join('actividads as a', 'a.id', '=', 'ap.actividad_id')
            ->select('p.programa', 'a.actividad', 'r.monto_orig', 'r.estado', 'rt.tipo_reforma', 'm.month as mes', 'i.cod_programa', 'i.cod_actividad', 'i.cod_item', 'i.item', 'i.grupo_gasto', 'ai.area_id')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('r.id', 'desc')
            ->get();

        $reforma2 = Reforma::from('reformas as r')
            ->with('pac_origen', 'pac_destino', 'area_item', 'user', 'reform_type')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('r.id', 'desc')
            ->get();


        //area a la que pertenece el usuario logueado
        $area_id = $request->user()->worker->departamento->area_id;

        //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
        $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
            $query->where('area_id', $area_id)
                ->where('departamento', 'like', "%direcc%");
        })->first();

        if (is_null($jefe_area)) {
            return redirect()->back()->with('message_danger', 'No se encontró el jefe de área para el usuario logueado y este es necesario para la firma del autorizado del documento');
        }

        $collection = Collection::make($reforma);

        $total_reforma = $collection->sum('monto_orig');

        $total_reforma2 = $reforma2->sum('monto_orig');

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

        $detalles_o2 = Reforma::from('reformas as r')
            ->with('pac_origen', 'area_item', 'user', 'reform_type')
            ->whereIn('r.id', $reformas_id)
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

        $todas = DB::table('pac_destino as pd')
            ->select('o.valor_orig', 'o.cod_programa as cod_programa_o', 'o.cod_actividad as cod_actividad_o', 'o.programa as programa_o', 'o.actividad as actividad_o', 'o.cod_item as cod_item_o', 'o.item as item_o', 'o.mes as mes_o', 'i.cod_programa', 'i.cod_actividad', 'p.programa', 'a.actividad', 'pac.cod_item', 'pac.item', 'm.month as mes', 'pd.valor_dest')
            ->join('pacs as pac', 'pac.id', '=', 'pd.pac_id')
            ->join('months as m', 'm.cod', '=', 'pac.mes')
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


        $pdf = PDF::loadView('reportes.reforma_select_pdf', compact('reforma', 'detalles_o', 'detalles_d', 'total_reforma', 'todas', 'jefe_area'));
        return $pdf->setPaper('letter', 'landscape')->stream('Reforma');//imprime en pantalla


    }

    /**
     * Generar el informe tecnico con los datos de las reformas seleccionadas
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generarInformeT(Request $request)
    {
        $reformas_id = $request->input('select_informes');//arreglo con los id de refromas

        $reformas = Reforma::from('reformas as r')
            ->with('pac_origen', 'pac_destino', 'area_item', 'user', 'reform_type','informe')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('r.id', 'desc')
            ->get();

        $primer_reforma=$reformas->first();
        $primer_reforma_tipo=$primer_reforma->reform_type_id; //id del tipo de reforma 2 o 3

        foreach($reformas as $r){

            //verificar que no exista una reforma en un informe
            if (count($r->informe)){
                return redirect()->back()->with('message_danger', 'No se pudo realizar el Informe Técnico porque la reforma No. '.$r->id.' se encuentra registrada en el Informe #'.$r->informe->numero.', con clasificación '.$r->informe->codificacion.'. Verifique la información.');
            }

            //verificar que no exista una reforma interna en las seleccionadas
            if ($r->reform_type->tipo_reforma==="INTERNA"){
                return redirect()->back()->with('message_danger', 'La reforma No. '.$r->id.' es interna y no se puede incluir en el Informe Técnico .');
            }

            //Ue las reformas sean de un mismo tipo INformativa o Ministerial
            if ($r->reform_type_id!=$primer_reforma_tipo) {
                return redirect()->back()->with('message_danger', 'Existen reformas combinadas. Debe filtrar las reformas de un mismo tipo (Informativa o Ministerial) para generar el Informe Técnico.');
            }

        }

        $cod_informe=null;
        if ($primer_reforma->reform_type->tipo_reforma==="INFORMATIVA"){
            $cod_informe='MODIF';
        }elseif (($primer_reforma->reform_type->tipo_reforma==="MINISTERIAL"))
            $cod_informe='MIN';

        $num = DB::table('informes')->where('codificacion',$cod_informe)->max('numero') + 1;

        $informe=new Informe();
        $informe->codificacion=$cod_informe;
        $informe->numero=$num;
        $informe->save();

        foreach($reformas as $r){
            $r->informe()->associate($informe);
            $r->update();
        }
        return redirect()->back()->with('message_success', 'Se creo el informe tecnico correctamente');
    }



    function printSeparator(Section $section)
    {
        $lineStyle = array('weight' => 1.5, 'width' => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(12), 'height' => 0, 'align' => 'center');
        $section->addLine($lineStyle);
    }

    public function informeTecnicoWord(){

        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');
        $year=$fecha_actual->year;

        $fecha=$fecha_actual->day.' '.$month.' de '.$year;

        $dir_area='MGS. CECILIA BALDA';
        $cargo_dir=' / DIRECTORA DE TALENTO HUMANO Y BIENESTAR INSTITUCIONAL';

        $phpWord=new PhpWord();

        // Begin code
        $section = $phpWord->addSection();

        //salto de pagina
//        $section = $phpWord->addSection();

        $fontStyleCabecera = 'textoCabecera';
        $phpWord->addFontStyle($fontStyleCabecera, ['name' => 'Arial', 'size' => 10, 'bold' => true ]);
        $paragraphStyleCabecera = 'parrafoCabecera';
        $phpWord->addParagraphStyle($paragraphStyleCabecera, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter' => 1]);


        $section->addImage(asset('images/fdg-logo.png'), ['width' => 150, 'padding'=> 5, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $section->addText('INFORME TÉCNICO DE MODIFICACIÓN AL PLAN OPERATIVO ANUAL '.$year,$fontStyleCabecera,$paragraphStyleCabecera);
        $section->addText('FDG-REF-POA'.$year.'-MIN-001 ',$fontStyleCabecera,$paragraphStyleCabecera);

        $section->addTextBreak();

        $textPara = $section->addTextRun();
        $textPara->addText('PARA:     ', ['bold'=>true]);
        $textPara->addText('ING. BLANCA SILVA ', ['bold'=>true]);
        $textPara->addText('/ DIRECTORA DE PLANIFICACIÓN Y CONTROL DE GESTIÓN ');

        $textDe = $section->addTextRun();
        $textDe->addText('DE:          ', ['bold'=>true]);
        $textDe->addText($dir_area, ['bold'=>true]);
        $textDe->addText($cargo_dir);

        $textFecha = $section->addTextRun();
        $textFecha->addText('FECHA:  ', ['bold'=>true]);
        $textFecha->addText($fecha);

        //linea horizontal separadora
        $this->printSeparator($section);

        $section->addText('Por medio de la presente solicito a usted, se autorice la reforma/reprogramación modificatoria
        correspondiente a la Planificación Operativa Anual '.$year.', de acuerdo al siguiente detalle:',['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);

        //Modificación al POA {{$reforma->cod_informe=='MIN' ? 'entre actividades' : 'misma actividad'}}
        $section->addText('Modificación al POA entre actividades: ', ['bold'=>true]);

        $section->addTextBreak(5);
        $section->addText('justificativos: ', ['bold'=>true]);
        $section->addTextBreak(5);

        $section->addTextBreak();
        $section->addText('Análisis de afectación de metas', ['bold'=>true]);

        $section->addText('Las modificaciones solicitadas en las actividades que conforman la Planificación Operativa
        Anual '.$year.', no afectarán a las metas planteadas ya que las mismas se cumplirán a medida que
        se ejecute el presupuesto.');

        $section->addText('Base Legal', ['bold'=>true]);
        $section->addText(' Según art. 74 del Reglamento Genereal a la Ley del Deporte, Educación Física y Recreación, establece "De las modificaciónes al POA.- Las organizaciones deportivas podrán, en función de sus necesidades debidamente justificadas, modificar su plan operativo anual aprobado por el Ministerio Sectorial de conformidad a las disposiciones por este último".');

        $section->addText('Documentos Habilitantes', ['bold'=>true]);
        $section->addText('Adjunto Matriz de  reforma/reprogramació POA '.$year);






        // Saving the document as OOXML file...
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        try {
            $objWriter->save(storage_path('test.docx'));
        }catch (\Exception $e){

        }
        return response()->download(storage_path('test.docx'));



    }


    /**
     * Exportar las reformas seleccionadas a plantilla en excel
     *
     * @param Request $request
     */

    public function reformasAllExcel(Request $request)
    {
//        if (Auth::user()->can('hacer-cierre')) {

        $fecha_actual = Carbon::now();
        $year = $fecha_actual->year;

        $reformas_id = $request->input('imp_reformas');//arreglo con los id de refromas

        if (empty($reformas_id) || !isset($reformas_id)) {
            return redirect()->back()->with('message_danger', 'Debe seleccionar al menos una reforma para poder imprmir');
        }

        $reformas = Reforma::from('reformas as r')
            ->with('pac_origen', 'pac_destino', 'area_item', 'user', 'reform_type')
            ->whereIn('r.id', $reformas_id)
            ->orderBy('r.id', 'desc')
            ->get();

        $user = $request->user();
        //area a la que pertenece el usuario logueado
        $area_id = $user->worker->departamento->area_id;

        $elaborado_nombre = '';
        $elaborado_cargo = '';
        $elaborado_ci = '';
        $aprobado_nombre = '';
        $aprobado_cargo = '';
        $aprobado_ci = '';

        if ($user->hasRole('root') || $user->hasRole('administrador')) {
            $elaborado_nombre = 'Ing. Xavier Omar Jacome Ortega';
            $elaborado_cargo = 'Administrador Financiero';
            $elaborado_ci = '0922385588';
            $aprobado_nombre = 'Arq. Rosa Edith Rada Alprecht';
            $aprobado_cargo = 'Administradora';
            $aprobado_ci = '0902885979';
        } elseif ($user->hasRole('analista')) {
            //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
            $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                $query->where('area_id', $area_id)
                    ->where('departamento', 'like', "%direcc%");
            })->first();

            if (is_null($jefe_area)) {
                return redirect()->back()->with('message_danger', 'No se encontró el jefe de área para el usuario logueado y este es necesario para la firma del autorizado del documento');
            }

            $elaborado_nombre = $user->worker->tratamiento . '. ' . $user->worker->getFullName();
            $elaborado_cargo = $user->worker->cargo;
            $elaborado_ci = $user->worker->num_doc;
            $aprobado_nombre = $jefe_area->tratamiento . '. ' . $jefe_area->getFullName();
            $aprobado_cargo = $jefe_area->cargo;
            $aprobado_ci = $jefe_area->num_doc;

        }

        $total_reforma = $reformas->sum('monto_orig');

        $reformasArray = [];
        $cont = 0;
        foreach ($reformas as $ref) {
            foreach ($ref->pac_destino as $r_pd) {
                $cont++;
                $reformasArray[] = [
                    'no1' => $cont,
                    'programa_o' => $r_pd->pac->area_item->item->actividad_programa->programa->programa,
                    'cod_atividad' => $r_pd->pac->area_item->item->cod_actividad . ' ' . $r_pd->pac->area_item->item->actividad_programa->actividad->actividad,
                    'cod_item' => $r_pd->reforma->area_item->item->cod_item,
                    'item' => $r_pd->reforma->area_item->item->item,
                    'mes_o' => $r_pd->reforma->area_item->month->month,
                    'monto_o' => '$ ' . number_format($r_pd->valor_dest, 2, '.', ' '),
                    'no2' => $cont,
                    'programa_d' => $r_pd->pac->area_item->item->actividad_programa->programa->programa,
                    'cod_atividad_d' => $r_pd->pac->area_item->item->cod_actividad . ' ' . $r_pd->pac->area_item->item->actividad_programa->actividad->actividad,
                    'cod_item_d' => $r_pd->pac->cod_item,
                    'item_d' => $r_pd->pac->item,
                    'mes_d' => $r_pd->pac->meses->month,
                    'monto_d' => '$ ' . number_format($r_pd->valor_dest, 2, '.', ' ')
                ];
            }
        }

        Excel::create('Matriz de modificacion del POA ' . ' - ' . Carbon::now() . '', function ($excel) use ($reformasArray, $year, $total_reforma, $cont, $elaborado_nombre, $elaborado_cargo, $elaborado_ci, $aprobado_nombre, $aprobado_cargo, $aprobado_ci) {

            $excel->sheet('Matriz', function ($sheet) use ($reformasArray, $year, $total_reforma, $cont, $elaborado_nombre, $elaborado_cargo, $elaborado_ci, $aprobado_nombre, $aprobado_cargo, $aprobado_ci) {

                //ancho de columnas
                $sheet->setWidth(['A' => 10.78, 'B' => 6.67, 'C' => 19.11, 'D' => 19.11, 'E' => 19.11, 'F' => 19.11, 'G' => 19.11, 'H' => 19.11,
                    'I' => 6.67, 'J' => 19.11, 'K' => 19.11, 'L' => 19.11, 'M' => 19.11, 'N' => 19.11, 'O' => 19.11,
                ]);

                //insertar imagen
                $objDrawing = new \PHPExcel_Worksheet_Drawing();
                $objDrawing->setPath(public_path('images/ministerio.png')); //your image path
                $objDrawing->setName('logo ministerio');
                $objDrawing->setCoordinates('B2');
                $objDrawing->setWidthAndHeight(140, 60);
                $objDrawing->setWorksheet($sheet);

                //encabezado
                $sheet->row(2, ['', 'MINISTERIO DEL DEPORTE']);
                $sheet->mergeCells('B2:O2');
                //$sheet->setCellValue('A10', $value); //valor en una celda
                $sheet->cells('B2:O2', function ($cells) { //en un rango de celdas
//                        $cells->setFontColor('#ffffff');
//                        $cells->setBackground('#404040');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                    // tipo de letra
//                        $cells->setFontFamily('Arial');
                    // tamaño de letra
//                        $cells->setFontSize(11);
                    // bordes (top, right, bottom, left)
//                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->row(3, ['', 'MATRIZ DE MODIFICACIÓN DEL PLAN OPERATIVO ANUAL ' . $year . ' ORGANISMOS DEPORTIVOS']);
                $sheet->mergeCells('B3:O3');
                $sheet->cells('B3:O3', function ($cells) {
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->setBorder('B5:E5', 'thin');
                $sheet->mergeCells('B5:E7');
                $sheet->setCellValue('B5', 'Nombre del Organismo Deportivo:');
                $sheet->cells('B5:E5', function ($cells) {
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
//                        $cells->setValue('Nombre del Organismo Deportivo:');
                });

                $sheet->setBorder('F5:G5', 'thin');
                $sheet->mergeCells('F5:G7');
                $sheet->setCellValue('F5', 'FEDERACIÓN DEPORTIVA DEL GUAYAS');
                $sheet->cells('F5:G5', function ($cells) {
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });


                $sheet->setBorder('B8:E8', 'thin');
                $sheet->mergeCells('B8:E9');
                $sheet->setCellValue('B8', 'Modificación al POA');
                $sheet->cells('B8:E8', function ($cells) {
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });


                $sheet->setBorder('F8:G8', 'thin');
                $sheet->mergeCells('F8:G9');
                $sheet->setCellValue('F8', '$ ' . number_format($total_reforma, 2, '.', ' '));
                $sheet->cells('F8:G8', function ($cells) {
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });


                $sheet->setBorder('B11:H11', 'thin');
                $sheet->mergeCells('B11:H11');
                $sheet->setCellValue('B11', 'Origen');
                $sheet->cells('B11:H11', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#8DB4E2');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->setBorder('I11:O11', 'thin');
                $sheet->mergeCells('I11:O11');
                $sheet->setCellValue('I11', 'Destino');
                $sheet->cells('I11:O11', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#C4D79B');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->getRowDimension(12)->setRowHeight(28.8);
                $sheet->getStyle('B12:O12')->getAlignment()->setWrapText(true);

                $sheet->setBorder('B12:O12', 'thin');
                $sheet->cells('B12:O12', function ($cells) {
                    $cells->setBackground('#CCC0DA');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');

                });
                $sheet->setCellValue('B12', 'No.');
                $sheet->setCellValue('C12', 'Programa');
                $sheet->setCellValue('D12', 'Número de Actividad');
                $sheet->setCellValue('E12', 'Código Ítem Presupuestario');
                $sheet->setCellValue('F12', 'Nombre del ítem Presupuestario');
                $sheet->setCellValue('G12', 'Mes Programado');
                $sheet->setCellValue('H12', 'Monto / Disminución');
                $sheet->setCellValue('I12', 'No.');
                $sheet->setCellValue('J12', 'Programa');
                $sheet->setCellValue('K12', 'Número de Actividad');
                $sheet->setCellValue('L12', 'Código Ítem Presupuestario');
                $sheet->setCellValue('M12', 'Nombre del ítem Presupuestario');
                $sheet->setCellValue('N12', 'Mes Programado');
                $sheet->setCellValue('o12', 'Monto / Incremento');

                $fila = 12;

                for ($i = 0; $i < $cont; $i++) {
                    $fila++;
                    $sheet->appendRow($fila, function ($row) use ($fila) {
                        //alineacion horizontal
                        $row->setAlignment('left');
//                        // alineacion vertical
                        $row->setValignment('center');
                        // tamaño de letra
                        $row->setFontSize(9);
                    });
                    $sheet->setBorder('B' . $fila . ':O' . $fila . '', 'thin');
                    $sheet->getStyle('B' . $fila . ':O' . $fila . '')->getAlignment()->setWrapText(true);
                }

                $fila = $fila + 1;
                $sheet->setBorder('B' . $fila . ':G' . $fila . '', 'thin');
                $sheet->mergeCells('B' . $fila . ':G' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', 'TOTAL DISMINUCIÓN');
                $sheet->cells('B' . $fila . ':G' . $fila . '', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#8DB4E2');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->setBorder('H' . $fila . '', 'thin');
                $sheet->setCellValue('H' . $fila . '', '$ ' . number_format($total_reforma, 2, '.', ' '));
                $sheet->cells('H' . $fila . '', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#8DB4E2');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->setBorder('I' . $fila . ':N' . $fila . '', 'thin');
                $sheet->mergeCells('I' . $fila . ':N' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', 'TOTAL INCREMENTO');
                $sheet->cells('I' . $fila . ':N' . $fila . '', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#C4D79B');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $sheet->setBorder('O' . $fila . '', 'thin');
                $sheet->setCellValue('O' . $fila . '', '$ ' . number_format($total_reforma, 2, '.', ' '));
                $sheet->cells('O' . $fila . '', function ($cells) {
//                        $cells->setFontColor('#ffffff');
                    $cells->setBackground('#C4D79B');
                    $cells->setFontWeight('bold');
                    //alineacion horizontal
                    $cells->setAlignment('center');
                    // alineacion vertical
                    $cells->setValignment('center');
                });

                $fila = $fila + 3;
                $sheet->mergeCells('B' . $fila . ':C' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', 'Elaborado por:');

                $sheet->mergeCells('I' . $fila . ':J' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', 'Autorizado por:');

                $fila = $fila + 3;
                $sheet->mergeCells('B' . $fila . ':E' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', '_______________________________________');

                $sheet->mergeCells('I' . $fila . ':L' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', '_______________________________________');

                $fila = $fila + 1;
                $sheet->mergeCells('B' . $fila . ':E' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', 'Nombre: ' . $elaborado_nombre . '');

                $sheet->mergeCells('I' . $fila . ':L' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', 'Nombre: ' . $aprobado_nombre . '');

                $fila = $fila + 1;
                $sheet->mergeCells('B' . $fila . ':E' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', 'Cargo: ' . $elaborado_cargo . '');

                $sheet->mergeCells('I' . $fila . ':L' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', 'Cargo: ' . $aprobado_cargo . '');

                $fila = $fila + 1;
                $sheet->mergeCells('B' . $fila . ':E' . $fila . '');
                $sheet->setCellValue('B' . $fila . '', 'CI: ' . $elaborado_ci . '');

                $sheet->mergeCells('I' . $fila . ':L' . $fila . '');
                $sheet->setCellValue('I' . $fila . '', 'CI: ' . $aprobado_ci . '');


                // Set all margins
//                $sheet->setPageMargin(0.25);
                // Set top, right, bottom, left margins
                $sheet->setPageMargin(array(
                    0.25, 0.30, 0.25, 0.30
                ));


                //crear la hoja a partir del array
                //5to parametro false pasa como encabesado de la primera fila los nombres de las columnas
                //4to parametro true, muestra cero como 0, sino muestar celda vacia
                $sheet->fromArray($reformasArray, null, 'B13', true, false);

            });

            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);

        })->export('xlsx');

    }

}
