<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Svg\Tag\Rect;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = trim($request->get('buscar'));
        $perPage = $request->input('perPage', 5);
        $roles = Role::where('name', 'LIKE', '%' . $buscar . '%')
                    ->paginate($perPage);
        return view('admin.roles.index',compact('roles','buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        $roles = Role::all();
        return view('admin.roles.create',compact('roles','permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
       
    ], [
        'name.required' => 'El campo Rol es obligatorio.',
        'name.regex' => 'El campo Rol solo permite letras y espacios.',
    ]);

    $role = Role::create($request->all());
    $role->permissions()->sync($request->permissions);
    

    return redirect()->route('admin.roles.index', $role);
}

    

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('admin.roles.show',compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
       
        return view('admin.roles.edit',compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
    
        $request->validate([
            'name' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
           
        ], [
            'name.required' => 'El campo Rol es obligatorio.',
            'name.regex' => 'El campo Rol solo permite letras y espacios.',
        ]);
        $role->update($request->all());

        $role->permissions()->sync($request->permissions);
        
        return redirect()->route('admin.roles.edit',$role,)->with('actualizado', 'ok');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {try {
        $role->delete();
        return redirect()->route('admin.roles.index', ['id' => $role->id])->with('eliminar', 'ok');
    } catch (QueryException $e) {
        if ($e->getCode() == 23000) {
            return redirect()->route('admin.roles.index', ['id' => $role->id])->with('error', 'ok');
        }    
    }
}
}