<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo','nombre','descripcion','precio_venta','stock','condicion'
    ];
}
