<?php

namespace App\Http\Controllers;

use App\DetalleIngreso;
use App\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
//        if (!$request->ajax()) return redirect('/');

        $buscar = $request -> buscar;
        $criterio = $request -> criterio;

        if ($buscar == ''){
            $ingresos = Ingreso::join('personas', 'ingresos.id', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                    'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto', 'ingresos.total',
                    'ingresos.estado', 'personas.nombre', 'users.usuario')
                ->orderBy('ingresos.id', 'desc')-> paginate(5);
        }
        else {
            $ingresos = Ingreso::join('personas', 'ingresos.id', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                    'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto', 'ingresos.total',
                    'users.usuario', 'users.password',
                    'ingresos.estado', 'personas.nombre', 'users.usuario')
                ->where('ingresos.' . $criterio, 'like', '%'. $buscar . '%')
                ->orderBy('ingresos.id', 'desc') -> paginate(5);
        }


        return [
            'pagination' => [
                'total'        => $ingresos-> total(),
                'current_page' => $ingresos-> currentPage(),
                'per_page'     => $ingresos-> perPage(),
                'last_page'    => $ingresos-> lastPage(),
                'from'         => $ingresos-> firstItem(),
                'to'           => $ingresos-> lastItem(),
            ],
            'ingresos' =>$ingresos
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{

            DB::beginTransaction();

            $mytime = Carbon::now('America/Mexico_City');

            $ingreso = new Ingreso();
            $ingreso-> fill($request->all());
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->fecha_hora = $mytime->toDateString();
            $ingreso->estado = 'Registrado';
            $ingreso -> save();

            $detalles = $request->data; //Array de detalles

            foreach ($detalles as $ep=>$det)
            {
                $detalle = new DetalleIngreso();
                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
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
        $ingreso = Ingreso::findOrFail($request -> id);
        $ingreso -> estado = "Anulado";
        $ingreso -> save();
    }
}
