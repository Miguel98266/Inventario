<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Insumo;

class InsumoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $insumos = Insumo::join('categorias','insumos.idcategoria','=','categorias.id')
            ->select('insumos.id','insumos.idcategoria','insumos.codigo','insumos.nombre','categorias.nombre as nombre_categoria','insumos.precio_insumo','insumos.stock','insumos.ubicacion','insumos.condicion')
            ->orderBy('insumos.id', 'desc')->paginate(10);
        }
        else{
            $insumos = Insumo::join('categorias','insumos.idcategoria','=','categorias.id')
            ->select('insumos.id','insumos.idcategoria','insumos.codigo','insumos.nombre','categorias.nombre as nombre_categoria','insumos.precio_insumo','insumos.stock','insumos.ubicacion','insumos.condicion')
            ->where('insumos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('insumos.id', 'desc')->paginate(10);

        }
         
 
        return [
            'pagination' => [
                'total'        => $insumos->total(),
                'current_page' => $insumos->currentPage(),
                'per_page'     => $insumos->perPage(),
                'last_page'    => $insumos->lastPage(),
                'from'         => $insumos->firstItem(),
                'to'           => $insumos->lastItem(),
            ],
            'insumos' => $insumos
        ];
    }

    public function listarInsumo(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $insumos = Insumo::join('categorias','insumos.idcategoria','=','categorias.id')
            ->select('insumos.id','insumos.idcategoria','insumos.codigo','insumos.nombre','categorias.nombre as nombre_categoria','insumos.precio_insumo','insumos.stock','insumos.ubicacion','insumos.condicion')
            ->orderBy('insumos.id', 'desc')->paginate(10);
        }
        else{
            $insumos = Insumo::join('categorias','insumos.idcategoria','=','categorias.id')
            ->select('insumos.id','insumos.idcategoria','insumos.codigo','insumos.nombre','categorias.nombre as nombre_categoria','insumos.precio_insumo','insumos.stock','insumos.ubicacion','insumos.condicion')
            ->where('insumos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('insumos.id', 'desc')->paginate(10);

        }
         
 
        return ['insumos' => $insumos];
    }

    public function buscarInsumo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $filtro=$request->filtro;
        $insumos= Insumo::where('codigo','=',$filtro)
        ->select('id','nombre')->orderBy('nombre','asc')->take(1)->get();

        return['insumos'=>$insumos];
    }



    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $insumo = new Insumo();
        $insumo->idcategoria = $request->idcategoria;
        $insumo->codigo = $request->codigo;
        $insumo->nombre = $request->nombre;
        $insumo->precio_insumo = $request->precio_insumo;
        $insumo->stock = $request->stock;
        $insumo->ubicacion = $request->ubicacion;
        $insumo->condicion = '1';
        $insumo->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $insumo = Insumo::findOrFail($request->id);
        $insumo->idcategoria = $request->idcategoria;
        $insumo->codigo = $request->codigo;
        $insumo->nombre = $request->nombre;
        $insumo->precio_insumo = $request->precio_insumo;
        $insumo->stock = $request->stock;
        $insumo->ubicacion = $request->ubicacion;
        $insumo->condicion = '1';
        $insumo->save();
    }
 
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $insumo = Insumo::findOrFail($request->id);
        $insumo->condicion = '0';
        $insumo->save();
    }
 
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $insumo = Insumo::findOrFail($request->id);
        $insumo->condicion = '1';
        $insumo->save();
    }
}
