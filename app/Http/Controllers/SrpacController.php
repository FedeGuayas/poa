<?php

namespace App\Http\Controllers;

use App\Cpac;
use App\Cpresupuestaria;
use App\Pac;
use App\Srpac;
use App\SrpacDestino;
use App\Worker;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SrpacController extends Controller
{

    /**
     * Crear Solicitud de reforma pac
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $pac_id = key($data);
        $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        $pacs = DB::table('area_item as ai')
            ->join('pacs as p', 'p.area_item_id', '=', 'ai.id')
            ->join('months as m', 'm.cod', '=', 'p.mes')
            ->join('items as i', 'ai.item_id', '=', 'i.id')
            ->join('areas as a', 'a.id', '=', 'ai.area_id')
            ->join('workers as w', 'w.id', '=', 'p.worker_id')
            ->join('departamentos as d', 'd.id', '=', 'w.departamento_id')
            ->select('p.id', 'p.cod_item', 'p.item', 'm.month as mes', 'm.cod', 'p.presupuesto', 'p.disponible','p.comprometido', 'p.devengado', 'p.liberado', 'p.reform', 'w.nombres', 'w.apellidos', 'w.id as trabajador_id', 'a.area', 'p.procedimiento', 'p.tipo_compra', 'p.concepto', 'p.comprometido', 'p.cpc', 'i.cod_programa', 'i.cod_actividad', 'd.area_id as area_trabajador', 'd.departamento', 'ai.area_id as aiID', 'p.proceso_pac', 'p.inclusion')
            ->get();


        return view('srpac.create', compact('pac', 'pacs', 'month', 'fecha_actual'));
    }

    /**
     * Generar PDF de solicitud de reforma pac y registro en la tabla de srpac
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        $rules = [
            'motivo' => 'required',
            'cod_item.*' => 'required|numeric',
            'cpc.*' => 'numeric',
            'tipo_compra.*' => 'required',
            'concepto.*' => 'required',
            'presupuesto.*' => 'required|numeric',
            'pac_origen_id' => 'required',
            'pac_id_destino.*' => 'required',
        ];

        $messages = [
            'motivo.required' => 'Debe seleccionar el/los motivo/s de la reforma',
            'cod_item.required' => 'La partida presupuestaria es requerida',
            'cod_item.numeric' => 'La partida presupuestaria debe ser un número',
            'cpc.numeric' => 'El CPC debe ser un número',
            'tipo_compra.required' => 'Debe seleccionar el tipo de compra',
            'concepto.required' => 'El detalle del producto es requerido',
            'presupuesto.required' => 'El presupuesto es requerido',
            'presupuesto.numeric' => 'El presupuesto debe ser un número',
            'pac_origen_id.required' => 'El pac es requerido',
            'pac_id_destino.required' => 'El pac destino es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $motivos = $request->input('motivo');//arreglo
        $cod_item = $request->input('cod_item');//arreglo
        $cpc = $request->input('cpc');//arreglo
        $tipo_compra = $request->input('tipo_compra');//arreglo
        $concepto = $request->input('concepto');//arreglo
        $presupuesto = $request->input('presupuesto');//arreglo
        $pac_id_destino = $request->input('pac_id_destino');//arreglo
        $pac_id = $request->input('pac_origen_id');

        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        $notas='';
        if (is_array($motivos)){
            $notas=implode(', ',$motivos);
        }

        if ($notas==''){
            return back()->withInput()->with('message_danger', 'No se pudo definir el motivo de la reforma');
        }

        try {
            DB::beginTransaction();

            //pac de origen
            $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

            if (!isset($pac)) {
                return back()->withInput()->with('message_danger', 'No se encontró el PAC de origen');
            }

            $area_id = $pac->area_item->area_id;

            //trabajadores que pertenecen al mismo area del trabajador logeado y esta en el departamento direccion (jefe de area)
            $jefe_area = Worker::whereHas('departamento', function ($query) use ($area_id) {
                $query->where('area_id', $area_id)
                    ->where('departamento', 'like', "%direcc%");
            })->first();

            if (!isset($jefe_area)) {
                return back()->withInput()->with('message_danger', 'No existe un jefe de área definido. Debe definir un jefe de área el cual debe estar en la Coordinación DIRECCION, esto es necesario para la firma del documento');
            }

            $srpac = new Srpac();
            $srpac->pac()->associate($pac);
            $srpac->cod_item = $pac->cod_item;
            $srpac->cpc = $pac->cpc;
            $srpac->tipo_compra = $pac->tipo_compra;
            $srpac->concepto = $pac->concepto;
            $srpac->presupuesto = round(($pac->presupuesto / 1.12), 2);   // floor(($pac->presupuesto / 1.12) * 100) / 100; //sin redondeo
            $srpac->notas = $notas;
            $srpac->user_sol_id = $request->user()->id;
            $srpac->save();

            $cont = 0;
            $necesario_reformar = 'N';
            $pac_control = [];
            while ($cont < count($pac_id_destino)) {

                if (in_array( $pac_id_destino[$cont], $pac_control)) {
                    $message = 'No debe agregar el mismo pac mas de una vez como destino';
                    return back()->withInput()->with('message_danger', $message);
                }

                $pac_control[] = $pac_id_destino[$cont];

                $pac_dest = Pac::where('id', $pac_id_destino[$cont])->first();//pac destino

                $srpac_dest = new SrpacDestino();
                $srpac_dest->srpac()->associate($srpac);
                $srpac_dest->pac()->associate($pac_dest);
                $srpac_dest->cod_item = $cod_item[$cont];
                $srpac_dest->cpc = $cpc[$cont];
                $srpac_dest->tipo_compra = $tipo_compra[$cont];
                $srpac_dest->concepto = strtoupper($concepto[$cont]);
                $srpac_dest->presupuesto = $presupuesto[$cont];
                $srpac_dest->save();

                if ($srpac->cod_item != $cod_item[$cont] || $srpac->presupuesto != $presupuesto[$cont]) {
                    $necesario_reformar = 'S'; //poner reform=1
                }

                $cont++;
            }

            if ($necesario_reformar == 'N') {
                $pac->srpac = Pac::APROBADA_SRPAC;
                $pac->reform = Pac::NO_REFORMAR_PAC;

            } elseif($necesario_reformar == 'S') {
                $pac->srpac = Pac::NO_APROBADA_SRPAC;
                $pac->reform = Pac::NECESARIA_REFORMA_PAC;
            }

            $pac->update();

            $pdf = PDF::loadView('srpac.srpac-pdf', compact('pac', 'month', 'fecha_actual', 'srpac', 'jefe_area'))->setPaper('A4', 'portrait');

            DB::commit();

            return $pdf->setPaper('A4', 'portrait')->download('SRPAC' . $srpac->id . '.pdf');

        } catch (\Exception $exception) {
            DB::rollback();
            return back()->with('message_danger', $exception->getMessage());
        }
    }

    /**
     * Actualizar el pdf de la SRPAC y adjuntar archivo en correo
     * @param Request $request
     * @return mixed
     */
    public function postFileSRPAC(Request $request)
    {
        $rules = [
            'srpac-file' => 'bail|required|mimes:pdf|max:100',
        ];

        $messages = [
            'srpac-file.required' => 'No selecciono ninguna archivo',
            'srpac-file.max' => 'El pdf no puede superar los 100Kb, pruebe con uno mas pequeño',
            'srpac-file.mimes' => ' El archivo debe ser un pdf',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors'=>$validator->messages()], 422);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $user_login = $request->user();

        try {

            DB::beginTransaction();

//        pac al que se hizo la srpac
            $pac = Pac::with('worker')->where('id', $request->input('srpac_pac_id'))->first();

            if (count($pac)>0) { //existe el pac

//            obtengo la ultima srpac del actual proceso de la tabla
                $srpac = Srpac::with('srpac_destino')->where('pac_id', $pac->id)->get()->last();

                if (isset($srpac)) //existe y no es null la SRPAC
                {
                    if ($request->hasFile('srpac-file')) {
                        //elimino archivo anterior
//                    $old_filename = public_path() . '/uploads/pac/srpac/' . $srpac->solicitud_file;
//                    \File::delete($old_filename);
                        //almaceno el nuevo archivo
                        $file = $request->file('srpac-file');
                        $name = 'SRPAC' . '' . $srpac->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                        $path = public_path() . '/uploads/pac/srpac';
                        $file->move($path, $name);
                        $srpac->solicitud_file = $name;
                    }
                    $srpac->user_aprueba_id = $user_login->id; //usuario que subio el archivo
                    $srpac->status = Srpac::SRPAC_ACTIVA; //solo se desactiva para permitir otra srpac, despues de la reforma
                    $srpac->update();

                    //una vez subida una srpac  si existe cpac deshabilitarla
                    $cpac = Cpac::where('pac_id', $pac->id)->get()->last();
                    if (count($cpac)>0){
                        $cpac->status=Cpac::CPAC_INACTIVA;
                        $cpac->update();
                    }

                    //una vez subida una srpac  si existe cert presup deshabilitarla
                    $cpresup = Cpresupuestaria::where('pac_id', $pac->id)->get()->last();
                    if (count($cpresup)>0){
                        $cpresup->status=Cpresupuestaria::CPRES_INACTIVA;
                        $cpresup->update();
                    }


                    //No es necesario reforma, se actualizan cpc, tipo_compra y detalle automaticamente
                    // $pac->srpac = Pac::APROBADA_SRPAC (automatico la salva en el pac); $pac->reform = Pac::NO_REFORMAR_PAC;
                    if ($pac->srpac == Pac::APROBADA_SRPAC && $pac->reform == Pac::NO_REFORMAR_PAC) {
                        //seleccinar los srpac_destino cuyo origen sea el mismo pac, solo asi actualizar cpc,tipo y detalle
                        foreach ($srpac->srpac_destino as $srpac_destino) {
                            if ($srpac_destino->pac_id == $srpac->pac_id) {
                                $pac->cpc = $srpac_destino->cpc;
                                $pac->tipo_compra = $srpac_destino->tipo_compra;
                                $pac->concepto = strtoupper($srpac_destino->concepto);
                            }
                        }
                        $this->sendSRPACUploadFileMail($pac, $srpac);
                        $pac->srpac = Pac::NO_APROBADA_SRPAC; //srpac=0
                        $pac->update();
                        $srpac->status = Srpac::SRPAC_INACTIVA; //se vuelve habilitar la generacion de srpac, sin necesidad de reforma
                        $srpac->update();
                    } //Necesario reforma   $pac->srpac = Pac::NO_APROBADA_SRPAC; $pac->reform = Pac::NECESARIA_REFORMA_PAC;
                    elseif ($pac->srpac == Pac::NO_APROBADA_SRPAC && $pac->reform == Pac::NECESARIA_REFORMA_PAC) {
                        foreach ($srpac->srpac_destino as $srpac_destino) {
                            if ($srpac_destino->pac_id == $srpac->pac_id) {
                                $pac->cpc = $srpac_destino->cpc;
                                $pac->tipo_compra = $srpac_destino->tipo_compra;
                                $pac->concepto = strtoupper($srpac_destino->concepto);
                            }
                        }

                        $this->sendSRPACUploadFileMail($pac, $srpac);
                        $pac->update();
                    }


                } else {
                    return back()->with(['message_danger' => 'NO existe solicitud de reforma para el pac seleccionado. No es posible subir el arhivo']);
                }
            } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

            DB::commit();
            $message='Se subió el archivo de SRPAC correctamente y se enviaron las notificaciones';
            if ($request->ajax()) {
                return response()->json(['message'=>$message], 200);
            } else {
                return back()->with(['message' => $message]);
            }

        } catch (\Exception $exception) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json(['message_error'=>'Error crítico: '.$exception->getMessage().'']);
            } else {
                return back()->with('message_danger', $exception->getMessage());
            }
        }
    }

    /**
     * Descargar el archivo pdf de la SRPAC
     * @param $id
     * @return mixed
     */
    public function SRPACDownload(Request $request, $pac_id)
    {
        $pac = Pac::where('id', $pac_id)->first();

        if ($pac) { //existe el pac

            $srpac = Srpac::where('pac_id', $pac->id)->get()->last();

            if (isset($srpac) && isset($srpac->solicitud_file)) //existe y no es null la srpac y ademas tiene un archivo cargado
            {
                $pathtoFile = public_path() . '/uploads/pac/srpac/' . $srpac->solicitud_file;
                return response()->download($pathtoFile);

            } else {
                return back()->with(['message_danger' => 'NO existe la SRPAC para el pac seleccionado. O no se encuentra el archivo']);
            }
        } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

    }


    /**
     * Correo de notificacion de subida de archivo de SRPAC a responsable del pac
     * @param $user
     * @param $pass
     */
    public function sendSRPACUploadFileMail($pac, $srpac)
    {
        $user_to = $pac->worker->email;//responsable del proceso
        $path = public_path() . '/uploads/pac/srpac/' . $srpac->solicitud_file;
        $name_file = $srpac->solicitud_file;

        Mail::send('emails.new_srpac_upload', ['pac' => $pac, 'srpac' => $srpac], function ($message) use ($user_to, $path, $name_file) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Solicitud de Reforma PAC aprobada');
            $message->to($user_to);
            $message->attach($path, ['as' => $name_file, 'mime' => 'application/pdf']);

        });

        if (Mail::failures()) {
            return back()->with(['message_danger' => 'Ocurrio un error al enviar la notificación']);

        }
    }


}
