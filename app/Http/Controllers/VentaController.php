<?php

namespace App\Http\Controllers;


use App\DetalleVenta;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request -> buscar;
        $criterio = $request -> criterio;
        if ($buscar == ''){
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                    'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto', 'ventas.total',
                    'ventas.estado', 'personas.nombre', 'users.usuario')
                ->orderBy('ventas.id', 'desc')-> paginate(5);
        }
        else {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                    'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto', 'ventas.total',
                    'ventas.estado', 'personas.nombre', 'users.usuario')
                ->where('ventas.' . $criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ventas.id', 'desc') -> paginate(5);
        }
        return [
            'pagination' => [
                'total'        => $ventas-> total(),
                'current_page' => $ventas-> currentPage(),
                'per_page'     => $ventas-> perPage(),
                'last_page'    => $ventas-> lastPage(),
                'from'         => $ventas-> firstItem(),
                'to'           => $ventas-> lastItem(),
            ],
            'ventas' =>$ventas
        ];
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request -> id;
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto', 'ventas.total',
                'ventas.estado', 'personas.nombre', 'users.usuario')
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')-> take(1)->get();
        return ['venta' => $venta];
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request -> id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select('detalle_ventas.cantidad', 'detalle_ventas.precio', 'detalle_ventas.descuento', 'articulos.nombre as articulo')
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        return ['detalles' =>$detalles];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $mytime = Carbon::now('America/Mexico_City');
            $venta = new Venta();
            $venta-> fill($request->all());
            $venta->idusuario = \Auth::user()->id;
            $venta->fecha_hora = $mytime->toDateTimeString();
            $venta->estado = 'Registrado';
            $venta -> save();
            $detalles = $request->data; //Array de detalles
            foreach ($detalles as $ep=>$det)
            {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request -> id);
        $venta -> estado = "Anulado";
        $venta -> save();
    }
}