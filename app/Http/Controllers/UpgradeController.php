<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpgradeController extends Controller
{
    // Mostrar página de upgrade
    public function show()
    {
        // Si ya es premiun o admin redirogir
        if (auth()->user()->isPremium()) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tienes acceso Premium');
        }

        return view('upgrade.show');
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();

        $user->update(['rol' => User::ROLE_REGISTRADO,]);

        return redirect()->route('dashboard')
            ->with('success', '¡Bienvenido a Premium! Ya tienes acceso a todas las funcionalidades');
    }
}
