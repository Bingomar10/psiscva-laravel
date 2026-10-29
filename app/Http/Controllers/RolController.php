<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        return view('roles.index');
    }

    public function listar()
    {
        $roles = Rol::select(['id', 'nombre'])->orderBy('id', 'desc')->get();

        return response()->json([
            "data" => $roles
        ]);
    }

    public function guardar(Request $request) {
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        try {
            $rolExistente = Rol::withTrashed()->where('nombre', $request->nombre)->first();

            if ($rolExistente) {
                if ($rolExistente->trashed()) {
                    $rolExistente->restore(); 
                    return response()->json([
                        'status' => 'success',
                        'message' => 'El rol ya existía y ha sido restaurado correctamente'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Este rol ya está registrado y activo en el sistema'
                    ], 422);
                }
            }

            // Crear un nuevo rol Y asociar permisos si se proporcionan
            $rol = Rol::create(['nombre' => $request->nombre]);

            if ($request->has('permisos')) {
                $rol->permisos()->sync($request->permisos);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rol creado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el rol: ' . $e->getMessage()
            ], 500);
        }
    }
    public function eliminar($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            $rol->delete(); 

            return response()->json([
                'status' => 'success',
                'message' => 'Rol eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar el rol'
            ], 500);
        }
    }

    // Busca un rol específico con sus permisos y devolvemos sus datos en formato JSON
    public function mostrar($id)
    {
        $rol = Rol::with('permisos')->findOrFail($id);
        return response()->json($rol);
    }

    // Actualiza el rol en la base de datos
    public function editar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        try {
            $rol = Rol::findOrFail($id);
            
            //Validamos que el nuevo nombre no esté en uso por otro rol
            $existe = Rol::withTrashed()->where('nombre', $request->nombre)->where('id', '!=', $id)->first();
            
            if ($existe) {
                if ($existe->trashed()) {
                    //Si existe pero está eliminado, lo borramos de la BD definitivamente para que se registre el nuevo nombre
                    $existe->forceDelete();
                } else {
                    return response()->json(['message' => 'El nombre del rol ya está en uso por un rol activo'], 422);
                }
            }

            $rol->update(['nombre' => $request->nombre]);

            // Sincronizamos: si un checkbox se desmarcó, lo quita. Si se marcó, lo agrega.
            if ($request->has('permisos')) {
                $rol->permisos()->sync($request->permisos);
            } else {
                // Si no se envió ninguno, significa que el usuario desmarcó todo
                $rol->permisos()->sync([]); 
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rol actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el rol'
            ], 500);
        }
    }

    
}