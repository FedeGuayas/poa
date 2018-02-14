<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Requests;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware(['role:root|administrador'],['except'=>['index','store']]);
        $this->middleware(['role:root|administrador']);
    }

    public function index(Request $request)
    {
        $roles=Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('admin-roles')) {
            return view('roles.create');
        }else return abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('admin-roles')) {
            $rol = new Role();
            $rol->name = $request->get('name');
            $rol->display_name = strtoupper($request->get('display_name'));
            $rol->description = strtoupper($request->get('description'));
            $rol->save();

            Session::flash('message', 'Rol creado correctamente');
            return redirect()->route('admin.roles.index');
        }else return abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rol=Role::findOrFail($id);
        return view('roles.show',compact('rol'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('admin-roles')) {
            $rol = Role::findOrFail($id);
            return view('roles.edit', compact('rol'));
        }else return abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->can('admin-roles')) {
            $rol = Role::findOrFail($id);
            $rol->name = $request->get('name');
            $rol->display_name = strtoupper($request->get('display_name'));
            $rol->description = strtoupper($request->get('description'));
            $rol->update();

            $request->session()->flash('message', 'Rol actualizado');
            return redirect()->route('admin.roles.index');
        }else return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->can('admin-roles')) {
            $rol = Role::findOrFail($id);

            $rol->delete();

            Session::flash('message_danger', 'Rol eliminado');
            return redirect()->route('admin.roles.index');
        }else return abort(403);
    }

    /**
     *Cargar vista para otorgar permisos a los roles
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public  function permisos($id)
    {

        $rol=Role::where('id',$id)->first();
        $role_perm = $rol->perms()->get();

        $role_permissions = $rol->perms()->get();

        $array=[];
        foreach($role_permissions as $rp){
            $array[]=[
                'name'=>$rp->display_name];
        }

        $permissions=Permission::all();
//        $perArray=[];
//        for($i=0; $i<count($role_perm);$i++){
//            $perArray[]=[
//                'id'=>$role_perm[$i]['id']
//            ];
//        }
//        foreach ($role_perm as $rp){
//            $rp->id;

        return view('roles.set-permisos',compact('rol','permissions'));
    }

    /**Actualizar permisos al rol
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public  function setPermisos(Request $request,$id)
    {

        $rol=Role::findOrFail($id);

        $perm=$request->get('permissions');

//        if (!$rol->perms()->get()->contains('id', $perm->id)) {
//            $rol->attachPermission($perm);
//        }

        if ($perm) {
//            $rol->attachPermissions($perm);
            $rol->perms()->sync($perm);
        }
        else{
            $rol->detachPermission($perm);
        }
        return redirect()->route('admin.roles.index')->with('message','Permisos actualizados');
    }

}