<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class SessionsController extends Controller
{
    public function create(){

        return view('auth.login');
    }

    public function store()
{
    if (!auth()->attempt(request(['email', 'password']))) {
        return back()->withErrors([
            'message' => 'Credenciales incorrectas. Por favor, intenta de nuevo.',
        ]);
    }
    if (auth()->check()) {
        return redirect()->route('admin.index');
    }
    return redirect()->route('login.index');
}
        public function destroy(){

            auth()->logout();
            return redirect()->route('login.index');
        }
}
