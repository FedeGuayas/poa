<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\ChangePasswordRequest;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador|responsable-poa'], ['except' => ['sendNewUserMail', 'getPasswordEdit','postPassword']]);
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with('worker')->get();

        return view('users.index', compact('users'));
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
    public function edit(Request $request, $id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

    }

    /**
     * Correo de notificacion por email de nuevo usuario
     * @param $user
     * @param $pass
     */
    public function sendNewUserMail($user, $pass)
    {
        Mail::send('emails.new_user', ['user' => $user, 'pass' => $pass], function ($message) use ($user) {
            $message->from('admin@fedeguayas.com.ec', 'Sistema Gestión del POA');
            $message->subject('Creación de cuenta de usuario');
            $message->to($user->email);
        });

    }

    /**
     * Cargar el form para editar la contraseña del usuario
     * @param Request $request
     * @return mixed
     */
    public function getPasswordEdit(Request $request)
    {

        $user = $request->user();

        return view('users.pass-edit', ['user' => $user]);
    }

    /**
     * Cambio de contraseña de usuario
     *
     * @param ChangePasswordRequest $request
     * @param User $user
     * @return mixed
     */
    public function postPassword(ChangePasswordRequest $request, User $user)
    {
        $new_pass = $request->password_new;
        $user->password = $new_pass;
        $user->update();
        return redirect()->back()->with('message', 'Contraseña Actualizada');
    }

    /**
     * Cargar vista para otorgar roles
     * @param $id
     * @return mixed
     */
    public function roles($id)
    {

        $user = User::where('id', $id)->with('worker')->first();
        $nombre = $user->worker->nombres . ' ' . $user->worker->apellidos;
//        $roles= [''=>'Seleccione roles'] + Role::lists('display_name', 'id')->all();
        $roles = Role::all();
        return view('users.set-roles', compact('user', 'roles', 'nombre'));

    }

    /**
     * Adicionar o quitar los roles del usuario.
     *
     * $id de usuario
     *
     */
    public function setRoles(Request $request, $id)
    {
        $user = User::where('id', $id)->with('worker')->first();
        $nombre = $user->worker->nombres . ' ' . $user->worker->apellidos;
        $roles = $request->get('roles');

        if ($roles) {
            $user->roles()->sync($roles);

        } else {
            // El usuario no marcó checkbox
            $user->detachRole($roles);
        }
        return redirect()->route('admin.users.index')->with('message', 'Accesos para ' . $nombre . ' actualizados');
    }

}
