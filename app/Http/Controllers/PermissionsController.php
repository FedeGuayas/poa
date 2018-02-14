<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Requests\StorePermissionRequest;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:root|administrador']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permisos=Permission::all();

        return view('permissions.index', compact('permisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePermissionRequest $request)
    {
        $permiso=new Permission;
        $permiso->name=$request->get('name');
        $permiso->display_name=strtoupper($request->get('display_name'));
        $permiso->description=strtoupper($request->get('description'));
        $permiso->save();

        Session::flash('message', 'Permiso creado correctamente');
        return redirect()->route('admin.permissions.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permiso=Permission::findOrFail($id);
        return view('permissions.edit',compact('permiso'));
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
        $permiso=Permission::findOrFail($id);
        $permiso->name=$request->get('name');
        $permiso->display_name=strtoupper($request->get('display_name'));
        $permiso->description=strtoupper($request->get('description'));
        $permiso->update();

        Session::flash('message','Permiso actualizado');
        return redirect()->route('admin.permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permiso=Permission::findOrFail($id);
        $permiso->delete();

        Session::flash('message_danger','Permiso eliminado');
        return redirect()->route('admin.permissions.index');
    }
}
