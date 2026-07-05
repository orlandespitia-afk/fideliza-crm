<?php

namespace Orlando\VoterLoyalty\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Definimos el rol de "Super" permitiendo variaciones de escritura en tu BD
    private $rolesDios = ['superadmin', 'super_admin', 'Superadmin'];

    /**
     * Muestra la lista de todos los usuarios registrados.
     */
    public function index()
    {
        // 🛡️ ESCUDO DE SEGURIDAD: Solo el Superadmin pasa.
        if (!Auth::check() || !in_array(Auth::user()->role, $this->rolesDios)) {
            return redirect()->to('/voters')->with('error', 'Acceso denegado: Solo el Super Administrador puede gestionar usuarios.');
        }

        $users = User::all();
        return view('voter-loyalty::users.index', compact('users'));
    }

    /**
     * Guarda un nuevo usuario en la BD.
     */
    public function store(Request $request)
    {
        // 🛡️ ESCUDO DE SEGURIDAD
        if (!Auth::check() || !in_array(Auth::user()->role, $this->rolesDios)) {
            return redirect()->to('/voters')->with('error', 'Acceso denegado: Solo el Super Administrador puede crear usuarios.');
        }

        $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:digitador,admin,super_admin',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'username' => $request->input('username'),
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'role'     => $request->input('role'),
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->back()->with('success', '¡Usuario creado con éxito!');
    }

    /**
     * Actualiza el rol o datos de un usuario existente.
     */
    public function update(Request $request, $id)
    {
        // 🛡️ Seguridad
        if (!Auth::check() || !in_array(Auth::user()->role, $this->rolesDios)) {
            return redirect()->to('/voters')->with('error', 'Acceso denegado.');
        }

        // Buscamos al usuario por su ID intacto
        $user = User::findOrFail($id);

        // Validamos TODOS los campos (Omitiendo validación de contraseña)
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:digitador,admin,super_admin',
        ]);

        // Actualizamos los datos (sin tocar contraseña ni ID)
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role = $request->input('role');

        // Guardamos los cambios
        $user->save();

        return redirect()->back()->with('success', '¡Datos del usuario actualizados con éxito!');
    }

    /**
     * Elimina un usuario de la BD.
     */
    public function destroy($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $this->rolesDios)) {
            return redirect()->to('/voters')->with('error', 'Acceso denegado.');
        }

        $user = User::findOrFail($id);

        // 🛡️ Evita que el Superadmin se elimine a sí mismo
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', '¡Acción bloqueada! No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        // Mensaje de éxito al eliminar
        return redirect()->back()->with('success', '¡Usuario eliminado permanentemente del sistema!');
    }
}
