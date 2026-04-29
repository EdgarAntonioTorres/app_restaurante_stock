<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required|in:administrador,gerente,cocinero',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect('/usuarios')->with('error', 'No puedes eliminarte a ti mismo');
        }
        $user->delete();
        return redirect('/usuarios')->with('success', 'Usuario eliminado');
    }
}