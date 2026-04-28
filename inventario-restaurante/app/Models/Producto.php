<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    protected $fillable = [
        'nombre',
        'categoria',
        'unidad',
        'stock_actual',
        'stock_minimo'
    ];
    
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}

