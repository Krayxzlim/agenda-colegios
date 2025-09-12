<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Taller;
use App\Models\Colegio;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $talleres = Taller::all();
        $colegios = Colegio::all();
        $usuarios = Usuario::all();
        return view('agenda.index', compact('talleres','colegios','usuarios'));
    }

    public function events()
    {
        $agenda = Agenda::with('taller','colegio','talleristas')->get();
        $events = $agenda->map(function($a){
            return [
                'id' => $a->id,
                'title' => $a->taller->nombre . ' - ' . $a->colegio->nombre,
                'start' => $a->fecha . 'T' . $a->hora,
                'taller_id' => $a->taller_id,
                'colegio_id' => $a->colegio_id,
                'talleristas' => $a->talleristas->pluck('id')->toArray()
            ];
        });
        return response()->json($events);
    }

    public function store(Request $request)
    {
        // Validar los datos principales
        $data = $request->validate([
            'taller_id'   => 'required|exists:talleres,id',
            'colegio_id'  => 'required|exists:colegios,id',
            'fecha'       => 'required|date',
            'hora'        => 'required',
        ]);

        // Crear o actualizar la agenda
        $agenda = $request->id
            ? Agenda::findOrFail($request->id)
            : new Agenda();

        $agenda->fill($data)->save();

        // Sincronizar los talleristas si están presentes
        if ($request->has('talleristas')) {
            $agenda->talleristas()->sync($request->talleristas);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Agenda::findOrFail($id)->delete();
        return response()->json(['success'=>true]);
    }
}
