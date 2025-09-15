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
        // Si viene con ID => es edición
        if ($request->id) {
            $usuario = Usuario::findOrFail($request->id);

            // Solo se puede cambiar rol
            $usuario->update([
                'rol' => $request->rol,
            ]);

        } else {
            // Validación creación
            $data = $request->validate([
                'nombre'   => 'required',
                'apellido' => 'required',
                'email'    => 'required|email|unique:usuarios,email',
                'password' => 'required|min:6',
                'rol'      => 'required|in:admin,tallerista',
            ]);

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
