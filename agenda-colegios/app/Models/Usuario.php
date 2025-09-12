<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\password;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'apellido', 'email','password', 'rol'];

    public function agenda()
    {
        return $this->belongsToMany(Agenda::class, 'agenda_talleristas', 'usuario_id', 'agenda_id');
    }
}
