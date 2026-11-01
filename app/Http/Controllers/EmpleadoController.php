<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use RahulHaque\Filepond\Facades\Filepond;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    public function index()
    {
        return view('empleados.index');
    }

    public function listar()
    {
        $empleados = Empleado::orderBy('id', 'desc')->get();
        return response()->json(["data" => $empleados]);
    }

        public function guardar(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|max:100',
            'apellido'  => 'required|max:100',
            'dui'       => 'required|max:10|unique:empleados,dui',
            'correo'    => 'required|email|max:150|unique:empleados,correo',
            'telefono'  => 'required|max:15',
            'direccion' => 'required|max:255',
            'foto'      => 'nullable|string' 
        ]);

        try {
            $data = $request->except('foto');

            if ($request->filled('foto')) {
                $file = Filepond::field($request->foto)->getFile();

                $rutaAbsoluta = $file->getRealPath();

                $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                $resultado = $cloudinary->uploadApi()->upload($rutaAbsoluta, [
                    'folder' => 'empleados'
                ]);

                $data['foto'] = $resultado['secure_url'];
                $data['foto_public_id'] = $resultado['public_id'];

                Filepond::field($request->foto)->delete();
            }

            Empleado::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Empleado registrado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function mostrar($id)
    {
        $empleado = Empleado::findOrFail($id);
        return response()->json($empleado);
    }

    public function editar(Request $request, $id)
{
    $empleado = Empleado::findOrFail($id);

    $request->validate([
        'nombre'    => 'required|max:100',
        'apellido'  => 'required|max:100',
        'dui'       => 'required|max:10|unique:empleados,dui,' . $id,
        'correo'    => 'required|email|max:150|unique:empleados,correo,' . $id,
        'telefono'  => 'required|max:15',
        'direccion' => 'required|max:255',
        'foto'      => 'nullable|string'
    ]);

    try {
        $data = $request->except('foto');

        if ($request->filled('foto')) {
            $file = Filepond::field($request->foto)->getFile();

            $rutaAbsoluta = $file->getRealPath();

            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

            $resultado = $cloudinary->uploadApi()->upload($rutaAbsoluta, [
                'folder' => 'empleados'
            ]);

            $data['foto'] = $resultado['secure_url'];
            $data['foto_public_id'] = $resultado['public_id'];

            if ($empleado->foto_public_id) {
                $cloudinary->uploadApi()->destroy($empleado->foto_public_id);
            }

            Filepond::field($request->foto)->delete();
        }

        $empleado->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Empleado actualizado correctamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ], 500);
    }
}

    public function eliminar($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            $empleado->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Empleado eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar al empleado'
            ], 500);
        }
    }
}