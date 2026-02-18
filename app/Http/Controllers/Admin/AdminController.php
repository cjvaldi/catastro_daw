<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Propiedad;
use App\Models\Busqueda;
use App\Models\LogApi;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_usuarios' => User::count(),
            'usuarios_premium' => User::where('rol', 'registrado')->count(),
            'propiedades_guardadas' => Propiedad::count(),
            'busquedas_realizadas' => Busqueda::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function usuarios()
    {
        $usuarios = User::latest()->paginate(20);
        return view('admin.usuarios', compact('usuarios'));
    }

    public function cambiarRol(User $user)
    {
        $nuevoRol = $user->rol === 'visitante' ? 'registrado' : 'visitante';
        $user->update(['rol' => $nuevoRol]);

        return back()->with('success', "Rol actualizado a: {$nuevoRol}");
    }

    public function toggleActivo(User $user)
    {
        $user->update(['activo' => !$user->activo]);

        $estado = $user->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$estado}");
    }

    public function logs()
    {
        $logs = LogApi::with('usuario')
            ->latest()
            ->paginate(50);

        return view('admin.logs', compact('logs'));
    }
}
