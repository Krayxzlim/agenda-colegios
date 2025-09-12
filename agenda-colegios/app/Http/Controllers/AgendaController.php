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

    public function getEvents()
    {
        $agendas = Agenda::with(['colegio', 'taller', 'talleristas'])->get();

        $events = $agendas->map(function ($agenda) {
            return [
                'id' => $agenda->id,
                'title' => $agenda->taller?->nombre . ' - ' . $agenda->colegio?->nombre,
                'start' => $agenda->fecha . 'T' . $agenda->hora,
                'extendedProps' => [
                    'colegio_id' => $agenda->colegio_id,
                    'taller_id' => $agenda->taller_id,
                    'talleristas' => $agenda->talleristas->pluck('id')->toArray(),
                ],
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'colegio_id' => 'required|exists:colegios,id',
            'taller_id' => 'required|exists:talleres,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'talleristas' => 'array|max:2',
        ]);

        $agenda = Agenda::updateOrCreate(
            ['id' => $request->id],
            $request->only('colegio_id', 'taller_id', 'fecha', 'hora')
        );

        // Sincronizar talleristas
        $agenda->talleristas()->sync($request->talleristas ?? []);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return response()->json(['success' => true]);
    }
}
