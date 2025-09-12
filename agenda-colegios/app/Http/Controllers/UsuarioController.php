<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'=>'required',
            'apellido'=>'required',
            'email'=>'required|email',
            'password'=>'nullable|min:6'
        ]);

        if($request->id){
            $usuario = Usuario::findOrFail($request->id);
            $usuario->update([
                'nombre'=>$data['nombre'],
                'apellido'=>$data['apellido'],
                'email'=>$data['email'],
                'password'=>$data['password'] ? Hash::make($data['password']) : $usuario->password
            ]);
        } else {
            $data['password'] = Hash::make($data['password']);
            Usuario::create($data);
        }
        return redirect()->route('usuarios.index');
    }

    public function destroy($id)
    {
        Usuario::findOrFail($id)->delete();
        return redirect()->route('usuarios.index');
    }
}
