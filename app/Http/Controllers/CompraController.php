<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Compra;
use App\DetalleCompra;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $compras = Compra::join('personas','compras.idproveedor','=','personas.id')
            ->join('users','compras.idusuario','=','users.id')
            ->select('compras.id','compras.tipo_comprobante','compras.serie_comprobante',
            'compras.fecha_hora','compras.total','compras.estado','personas.nombre',
            'users.usuario')
            ->orderBy('compras.id', 'desc')->paginate(7);
        }
        else{
            $compras = Compra::join('personas','compras.idproveedor','=','personas.id')
            ->join('users','compras.idusuario','=','users.id')
            ->select('compras.id','compras.tipo_comprobante','compras.serie_comprobante',
            'compras.fecha_hora','compras.total','compras.estado','personas.nombre',
            'users.usuario')
            ->where('compras.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('compras.id', 'desc')->paginate(7);
        }
         
 
        return [
            'pagination' => [
                'total'        => $compras->total(),
                'current_page' => $compras->currentPage(),
                'per_page'     => $compras->perPage(),
                'last_page'    => $compras->lastPage(),
                'from'         => $compras->firstItem(),
                'to'           => $compras->lastItem(),
            ],
            'compras' => $compras
        ];
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $id = $request->id;
            $compra = Compra::join('personas','compras.idproveedor','=','personas.id')
            ->join('users','compras.idusuario','=','users.id')
            ->select('compras.id','compras.tipo_comprobante','compras.serie_comprobante',
            'compras.fecha_hora','compras.total','compras.estado','personas.nombre',
            'users.usuario')
            ->where('compras.id','=',$id)
            ->orderBy('compras.id', 'desc')->take(1)->get();
 
        return ['compra' => $compra];
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $id = $request->id;
            $detalles = DetalleCompra::join('insumos','detalle_compras.idinsumo','=','insumos.id')
            ->select('detalle_compras.cantidad','detalle_compras.precio_compra as precio','insumos.nombre as insumo')
            ->where('detalle_compras.idcompra','=',$id)
            ->orderBy('detalle_compras.id', 'desc')->get();
 
        return ['detalles' => $detalles];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        try{
            DB::beginTransaction();

            $mytime=Carbon::now('America/Mexico_City');

            $compra = new Compra();
            $compra->idproveedor=$request->idproveedor;
            $compra->idusuario=\Auth::user()->id;
            $compra->tipo_comprobante=$request->tipo_comprobante;
            $compra->serie_comprobante=$request->serie_comprobante;
            $compra->fecha_hora=$mytime->toDateString();   
            $compra->total=$request->total;
            $compra->estado='Registrado';
            $compra->save();

            $detalles=$request->data;
            foreach($detalles as $ep=>$det)
            {
                $detalle=new DetalleCompra();
                $detalle->idcompra=$compra->id;
                $detalle->idinsumo=$det['idinsumo'];
                $detalle->cantidad=$det['cantidad'];
                $detalle->precio_compra=$det['precio'];
                $detalle->save();

            }


            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        
        
        
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $compra = Compra::findOrFail($request->id);
        $compra->estado = 'Cancelada';
        $compra->save();
    }
}
