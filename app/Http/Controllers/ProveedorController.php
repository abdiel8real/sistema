<?php

namespace App\Http\Controllers;

use App\Proveedor;
use App\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request -> buscar;
        $criterio = $request -> criterio;

        if ($buscar == ''){
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
            'personas.num_documento', 'personas.direccion', 'personas.telefono',
            'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->orderBy('personas.id', 'desc')-> paginate(5);
        }
        else {
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
            'personas.num_documento', 'personas.direccion', 'personas.telefono',
            'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->where('personas.' . $criterio, 'like', '%'. $buscar . '%')
            ->orderBy('personas.id', 'desc') -> paginate(5);
        }


        return [
            'pagination' => [
                'total'        => $personas-> total(),
                'current_page' => $personas-> currentPage(),
                'per_page'     => $personas-> perPage(),
                'last_page'    => $personas-> lastPage(),
                'from'         => $personas-> firstItem(),
                'to'           => $personas-> lastItem(),
            ],
            'personas' =>$personas
        ];
    }

    public function selectProveedor(Request $request)
    {
//        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
        ->where('personas.nombre', 'like', '%' . $filtro . '%')
        ->orWhere('personas.num_documento', 'like', '%' . $filtro . '%')
        ->select('personas.id', 'personas.nombre', 'personas.num_documento')
        ->orderBy('personas.nombre', 'asc')->get();

        return ['proveedores' => $proveedores];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{

            DB::beginTransaction();
            $persona = new Persona();
            $persona -> fill($request->all());
            $persona -> save();

            $proveedor = new Proveedor();
            $proveedor ->fill($request->all());
            $proveedor -> id = $persona->id;
            $proveedor ->save();

            DB::commit();


        } catch (Exception $e){

            DB::rollBack();

        }

    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{

            DB::beginTransaction();

            $proveedor = Proveedor::findOrFail($request->id);

            $persona = Persona::findOrFail($request->id);

            $persona -> fill($request->all());
            $persona -> save();

            $proveedor ->fill($request->all());
            $proveedor -> id = $persona->id;
            $proveedor ->save();

            DB::commit();


        } catch (Exception $e){

            DB::rollBack();

        }
    }
}
