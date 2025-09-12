<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;

class TallerController extends Controller
{
    public function index()
    {
        $talleres = Taller::all();
        return view('talleres.index', compact('talleres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'=>'required',
            'descripcion'=>'nullable'
        ]);

        if($request->id){
            Taller::findOrFail($request->id)->update($data);
        } else {
            Taller::create($data);
        }
        return redirect()->route('talleres.index');
    }

    public function destroy($id)
    {
        Taller::findOrFail($id)->delete();
        return redirect()->route('talleres.index');
    }
}
