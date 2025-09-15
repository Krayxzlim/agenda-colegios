<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';
    protected $fillable = ['colegio_id', 'taller_id', 'fecha', 'hora'];

    public function colegio()
    {
        return $this->belongsTo(Colegio::class);
    }

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }

    public function talleristas()
    {
        return $this->belongsToMany(Usuario::class, 'agenda_talleristas', 'agenda_id', 'usuario_id');
    }
}
