<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table='detalle_compras';
    protected $fillable=[
        'idcompra',
        'idinsumo',
        'cantidad',
        'precio'
    ];

    public $timestamps=false;
}
