<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\Taller;
use App\Models\Usuario;
use App\Models\Colegio;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;

class ReporteController extends Controller
{
    public function index()
    {
        $colegios = Colegio::all();
        $talleres = Taller::all();
        $usuarios = Usuario::all();
        return view('reportes.index', compact('colegios','talleres','usuarios'));
    }

    public function filtrar(Request $request)
    {
        $tipo = $request->tipo;
        $desde = $request->desde;
        $hasta = $request->hasta;
        $colegio_id = $request->colegio_id;
        $tallerista_id = $request->tallerista_id;
        $taller_id = $request->taller_id;

        $query = Agenda::with(['colegio','taller'])->orderBy('fecha');

        if ($desde) $query->where('fecha', '>=', $desde);
        if ($hasta) $query->where('fecha', '<=', $hasta);
        if ($colegio_id) $query->where('colegio_id', $colegio_id);
        if ($taller_id) $query->where('taller_id', $taller_id);
        if ($tallerista_id) $query->whereJsonContains('talleristas', $tallerista_id);

        switch($tipo){
            case 'por_mes':
                $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                $result = $query->selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, COUNT(*) as cantidad')
                    ->groupBy('año','mes')
                    ->get()
                    ->map(function($r) use ($meses) {
                        return [
                            'Año' => $r->año,
                            'Mes' => $meses[$r->mes] ?? $r->mes,
                            'Cantidad' => $r->cantidad
                        ];
                    });
                break;

            case 'por_colegio':
                $result = $query->get()->groupBy(function($a){
                        return $a->colegio->nombre ?? 'Sin asignar';
                    })
                    ->map(function($group,$nombre){
                        return ['Colegio'=>$nombre,'Cantidad'=>$group->count()];
                    })->values();
                break;

            case 'por_tallerista':
                $data = [];
                $agendas = $query->with('talleristas')->get(); // query talleristas
                foreach($agendas as $evento){
                    $colegio = $evento->colegio->nombre ?? 'Sin asignar';
                    $taller = $evento->taller->nombre ?? 'Sin asignar';
                    $fecha = $evento->fecha;
                    $hora = $evento->hora ?? '';
                    foreach($evento->talleristas as $t){
                        // fltro x tallerista
                        if($tallerista_id && $t->id != $tallerista_id) continue;

                        $data[] = [
                            'Colegio' => $colegio,
                            'Taller' => $taller,
                            'Tallerista' => $t->nombre . ' ' . $t->apellido,
                            'Fecha' => $fecha,
                            'Hora' => $hora
                        ];
                    }
                }
                $result = collect($data);
                break;

            case 'por_tema':
                $result = $query->get()->groupBy(function($a){ return $a->taller_id; })
                    ->map(function($group){
                        $taller = $group[0]->taller->nombre ?? 'Sin asignar';
                        $desc = $group[0]->taller->descripcion ?? '';
                        return [
                            'Taller' => $taller,
                            'Cantidad' => $group->count(),
                            'Descripción' => $desc
                        ];
                    })->values();
                break;

            default:
                $result = collect();
        }

        return response()->json($result);
    }

    public function export(Request $request)
    {
        $tipo = $request->tipo;
        $desde = $request->desde;
        $hasta = $request->hasta;
        $colegio_id = $request->colegio_id;
        $tallerista_id = $request->tallerista_id;
        $taller_id = $request->taller_id;

        return Excel::download(new ReporteExport($tipo,$desde,$hasta,$colegio_id,$tallerista_id,$taller_id), 'reporte.xlsx');
    }
}
