<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaTallerista extends Model
{
    use HasFactory;

    protected $table = 'agenda_talleristas';
    protected $fillable = ['agenda_id', 'usuario_id'];
}
