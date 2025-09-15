<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'apellido', 'email','password', 'rol'];

    protected $hidden = ['password', 'remember_token'];

    public function agenda()
    {
        return $this->belongsToMany(Agenda::class, 'agenda_talleristas', 'usuario_id', 'agenda_id');
    }
}
