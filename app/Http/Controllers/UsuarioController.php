<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index');
    }

    public function listar()
    {
        $usuarios = User::with(['empleado', 'rol'])->orderBy('id', 'desc')->get();
        return response()->json(["data" => $usuarios]);
    }

    public function datosFormulario(Request $request)
    {
        $roles = Rol::all();        
        // Solo empleados que no tengan un usuario asignado
        $empleados = Empleado::doesntHave('usuario')->get(); 

        $nuevoUsername = '';
        if ($request->filled('empleado_id')) {
            $empleado = Empleado::find($request->empleado_id);
            $nuevoUsername = $this->generarUsername($empleado);
        }

        return response()->json([
            'roles' => $roles,
            'empleados' => $empleados,
            'nuevo_username' => $nuevoUsername
        ]);
    }

    private function generarUsername($empleado)
    {
        $apellidos = explode(' ', trim($empleado->apellido));
        
        if (count($apellidos) >= 2) {
            $iniciales = strtoupper(substr($apellidos[0], 0, 1) . substr($apellidos[1], 0, 1));
        } else {
            $iniciales = strtoupper(substr($apellidos[0] ?? 'XX', 0, 2));
        }
        
        $anio = date('y');
        $prefijo = $iniciales . $anio;
        
        $ultimoUsuario = User::withTrashed()
            ->where('nombre_usuario', 'like', $prefijo . '%')
            ->orderBy('nombre_usuario', 'desc')
            ->first();

        $correlativo = 1;
        if ($ultimoUsuario) {
            $ultimoCorrelativo = intval(substr($ultimoUsuario->nombre_usuario, strlen($prefijo)));
            $correlativo = $ultimoCorrelativo + 1;
        }

        return $prefijo . str_pad($correlativo, 3, '0', STR_PAD_LEFT);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id|unique:usuarios,empleado_id',
            'rol_id' => 'required|exists:roles,id',
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario',
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ]
        ]);

        try {
            User::create([
                'empleado_id' => $request->empleado_id,
                'rol_id' => $request->rol_id,
                'nombre_usuario' => $request->nombre_usuario,
                'password' => Hash::make($request->password),
                'activo' => 1
            ]);

            return response()->json(['status' => 'success', 'message' => 'Usuario creado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }

    public function mostrar($id)
    {
        $usuario = User::with(['empleado', 'rol'])->findOrFail($id);
        return response()->json($usuario);
    }

        public function editar(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'rol_id' => 'required|exists:roles,id',
        ]);

        try {
            $usuario->update([
                'rol_id' => $request->rol_id
            ]);

            return response()->json(['status' => 'success', 'message' => 'Usuario actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }

     public function cambiarEstado(int $id)
    {
        try {
            $usuario = User::findOrFail($id);
            //No permitir que el usuario logueado se desactive a sí mismo
            if (Auth::id() == $id) {
                return response()->json(['status' => 'error', 'message' => 'No puedes desactivar tu propia cuenta de usuario'], 400);
            }

            if ($usuario->rol && $usuario->rol->nombre === 'Administrador') {
                return response()->json(['status' => 'error', 'message' => 'Por seguridad, no se puede deshabilitar a un usuario con rol de Administrador'], 400);
            }

            // Invertimos el estado: si es 1 pasa a 0, y si es 0 pasa a 1
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            $mensaje = $usuario->activo ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente';

            return response()->json(['status' => 'success', 'message' => $mensaje]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'No se pudo cambiar el estado del usuario'], 500);
        }
    }

    /*
    public function eliminar($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            // Validación extra: No permitir que el usuario logueado se elimine a sí mismo
            if (Auth::id() == $id) {
                return response()->json(['status' => 'error', 'message' => 'No puedes eliminar tu propia cuenta de usuario'], 400);
            }

            $usuario->delete();
            return response()->json(['status' => 'success', 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'No se pudo eliminar el usuario'], 500);
        }
    }
    */
}