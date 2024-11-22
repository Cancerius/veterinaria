<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
class RegisterController extends Controller


{
    

    public function create(){
       
        return view('auth.register');
    }

    public function store()
    {
        $this->validate(request(), [
            'nombre_completo' => 'required',
            'email'=> 'required|email',
            'password' =>'required|confirmed',
        ]);
    
        // Crea el veterinario y asigna el ID del rol de administrador
        $veterinario = User::create([
            'nombre_completo' => request('nombre_completo'),
            'email' => request('email'),
            'password' => request('password'),
            
        ]);
    
        auth()->login($veterinario);
        return redirect()->route('admin.index');
    }
}
