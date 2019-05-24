<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'idcategoria','codigo','nombre','precio_insumo','ubicacion','stock','condicion'
    ];
   
    public function categoria(){
        return $this->belongsTo('App\Categoria');
    }
}
