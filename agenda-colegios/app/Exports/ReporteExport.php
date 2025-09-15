<?php

namespace App\Exports;

use App\Models\Agenda;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteExport implements FromCollection, WithHeadings
{
    protected $tipo;
    protected $desde;
    protected $hasta;
    protected $colegio_id;
    protected $tallerista_id;

    public function __construct($tipo, $desde=null, $hasta=null, $colegio_id=null, $tallerista_id=null)
    {
        $this->tipo = $tipo;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->colegio_id = $colegio_id;
        $this->tallerista_id = $tallerista_id;
    }

    public function collection()
    {
        $query = Agenda::query();

        if($this->desde) $query->where('fecha','>=',$this->desde);
        if($this->hasta) $query->where('fecha','<=',$this->hasta);
        if($this->colegio_id) $query->where('colegio_id',$this->colegio_id);

        switch($this->tipo){
            case 'por_mes':
                $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                $result = $query->selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, COUNT(*) as cantidad')
                    ->groupBy('año','mes')
                    ->get()
                    ->map(function($r) use($meses){
                        return [
                            'Año'=>$r->año,
                            'Mes'=>$meses[$r->mes] ?? $r->mes,
                            'Cantidad'=>$r->cantidad
                        ];
                    });
                return $result->toArray();

            case 'por_colegio':
                $result = $query->get()->groupBy(function($a){
                        return $a->colegio->nombre ?? 'Sin asignar';
                    })
                    ->map(function($g,$nombre){
                        return ['Colegio'=>$nombre,'Cantidad'=>$g->count()];
                    })
                    ->values();
                return $result->toArray();

            case 'por_tallerista':
                $data = [];
                foreach($query->get() as $evento){
                    $colegio = $evento->colegio->nombre ?? 'Sin asignar';
                    $taller = $evento->taller->nombre ?? 'Sin asignar';
                    $fecha = $evento->fecha;
                    $hora = $evento->hora ?? '';
                    foreach($evento->talleristas ?? [] as $t){
                        if($this->tallerista_id && $t['id'] != $this->tallerista_id) continue;
                        $data[] = [
                            'Colegio'=>$colegio,
                            'Taller'=>$taller,
                            'Tallerista'=>$t['nombre_completo'],
                            'Fecha'=>$fecha,
                            'Hora'=>$hora
                        ];
                    }
                }
                return $data;

            case 'por_tema':
                $result = $query->get()->groupBy(function($a){ return $a->taller_id; })
                    ->map(function($g){
                        return [
                            'Taller'=>$g[0]->taller->nombre ?? 'Sin asignar',
                            'Cantidad'=>$g->count(),
                            'Descripción'=>$g[0]->taller->descripcion ?? ''
                        ];
                    })
                    ->values();
                return $result->toArray();

            default: return [];
        }
    }

    public function headings(): array
    {
        switch($this->tipo){
            case 'por_mes': return ['Año','Mes','Cantidad'];
            case 'por_colegio': return ['Colegio','Cantidad'];
            case 'por_tallerista': return ['Colegio','Taller','Tallerista','Fecha','Hora'];
            case 'por_tema': return ['Taller','Cantidad','Descripción'];
            default: return [];
        }
    }
}
