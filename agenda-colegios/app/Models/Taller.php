<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    use HasFactory;

    protected $table = 'talleres';
    protected $fillable = ['nombre', 'descripcion', 'capacidad'];

    public function agenda()
    {
        return $this->hasMany(Agenda::class);
    }
}
