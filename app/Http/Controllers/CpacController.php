<?php

namespace App\Http\Controllers;

use App\Cpac;
use App\Pac;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CpacController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Generar PDF de certificacion de pac y registro en la tabla de certificacion
     * @param Request $request
     * @param $pac_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function certificacionPDF(Request $request, $pac_id)
    {
        setlocale(LC_TIME, 'es');
        $fecha_actual = Carbon::now();
        $month = $fecha_actual->formatLocalized('%B');//mes en español

        try {
            DB::beginTransaction();

            $pac = Pac::with('worker', 'area_item')->where('id', $pac_id)->first();

            if (!isset($pac)) {
                return back()->withInput()->with('message_danger', 'No se encontró el PAC');
            }

            if ($pac) { //si existe el pac, crear la certificacion
                $cpac = new Cpac();
                $cpac->pac()->associate($pac);
                $cpac->partida = $pac->cod_item;
                $cpac->cpc = $pac->cpc;
//                $cpac->monto = floor(($pac->comprometido / 1.12) * 100) / 100; //sin redondeo
                $cpac->monto = round((($pac->disponible) / 1.12), 2);
                $cpac->user_sol_id = $request->user()->id;
                $cpac->save();
            }
            DB::commit();

            $pdf = PDF::loadView('cpacs.cpac-pdf', compact('cpac', 'month', 'fecha_actual'))->setPaper('A4', 'portrait');
            return $pdf->setPaper('A4', 'portrait')->download('CPAC' . $cpac->id . '.pdf');
        } catch (\Exception $exception) {
            DB::rollback();
        }
    }

    /**
     * Subir el pdf de la CPAC autorizado
     * @param Request $request
     * @return mixed
     */
    public function postFileCPAC(Request $request)
    {
        $rules = [
            'cpac-file' => 'bail|required|mimes:pdf|max:100',
        ];

        $messages = [
            'required' => 'No selecciono ninguna archivo',
            'max' => 'El pdf no puede superar los 100Kb, pruebe con uno mas pequeño',
            'mimes' => ' El archivo debe ser un pdf',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors'=>$validator->messages()], 422);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        try {

            DB::beginTransaction();

            $pac = Pac::with('area_item')->where('id', $request->input('cpac_pac_id'))->first();

            $cpac = Cpac::where('pac_id', $pac->id)->get()->last();

            if (is_null($cpac)){
                $message= 'No se ha generado la CPAC';
                if ($request->ajax()) {
                    return response()->json(['message_error'=>$message], 200);
                } else {
                    return back()->with(['message_danger' => $message]);
                }
            }


            if ($request->hasFile('cpac-file')) {

                //elimino archivo anterior
//                    $old_filename = public_path() . '/uploads/pac/certifications/' . $cpac->certificacion;
//                    \File::delete($old_filename);

                //almaceno el nuevo archivo
                $file = $request->file('cpac-file');
                $name = 'CPAC' . '' . $cpac->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/pac/certifications';
                $file->move($path, $name);
                $cpac->certificado = $name;
            }
            $cpac->status=Cpac::CPAC_ACTIVA;
            $cpac->update();


            $this->sendCPACUploadFileMail($pac, $cpac);


            DB::commit();
            $message='Se subió el archivo la CPAC correctamente y se enviaron las notificaciones';
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
     * Descargar el archivo pdf de la CPAC
     * @param $id
     * @return mixed
     */
    public function CPACDownload(Request $request, $pac_id)
    {
        $pac = Pac::where('id', $pac_id)->first();

        if ($pac) { //existe el pac

            $cpac = Cpac::where('pac_id', $pac->id)->get()->last();

            if (isset($cpac) && isset($cpac->certificado)) //existe y no es null la certificacion del pac CPAC
            {
                $pathtoFile = public_path() . '/uploads/pac/certifications/' . $cpac->certificado;
                return response()->download($pathtoFile);

            } else {
                return back()->with(['message_danger' => 'NO existe certificaión para el pac seleccionado. O no se encuentra la CPAC']);
            }
        } else return back()->with(['message_danger' => 'Error!, PAC no encontrado']);

    }

    /**
     * Correo de notificacion de subida de archivo de CPAC a responsable del pac
     * @param $user
     * @param $pass
     */
    public function sendCPACUploadFileMail($pac, $cpac)
    {
        $user_to = $pac->worker->email; //responsable del pac
        $path = public_path() . '/uploads/pac/certifications/' . $cpac->certificado;
        $name_file = $cpac->certificado;

        Mail::send('emails.new_cpac_upload', ['pac' => $pac, 'cpac' => $cpac], function ($message) use ($user_to, $path, $name_file) {

            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Certificación PAC aprobada');
            $message->to($user_to);
            $message->attach($path, ['as' => $name_file, 'mime' => 'application/pdf']);

        });

        if (Mail::failures()) {
            return back()->with(['message_danger' => 'Ocurrio un error al enviar la notificación']);

        }
    }

}
