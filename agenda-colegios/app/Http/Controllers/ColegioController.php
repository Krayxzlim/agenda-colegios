<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use Illuminate\Http\Request;

class ColegioController extends Controller
{
    public function index()
    {
        $colegios = Colegio::all();
        return view('colegios.index', compact('colegios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'=>'required',
            'direccion'=>'nullable'
        ]);

        if($request->id){
            Colegio::findOrFail($request->id)->update($data);
        } else {
            Colegio::create($data);
        }
        return redirect()->route('colegios.index');
    }

    public function destroy($id)
    {
        Colegio::findOrFail($id)->delete();
        return redirect()->route('colegios.index');
    }
}
