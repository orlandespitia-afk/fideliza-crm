<?php

namespace Orlando\VoterLoyalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Orlando\VoterLoyalty\Models\VoterRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class VoterController extends Controller
{
    /**
     * 1. Muestra el formulario de inicio de sesión de la campaña.
     * Si el usuario ya está autenticado en el sistema, lo redirige automáticamente al panel.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->to('/voters');
        }

        return view('voter-loyalty::login');
    }

    /**
     * 2. Procesa el intento de autenticación / inicio de sesión.
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->to('/voters');
        }

        return back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('username'));
    }

    /**
     * 3. Listado principal de votantes.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $porPagina = $request->input('per_page', 10);
        if (!in_array($porPagina, [10, 30, 50])) {
            $porPagina = 10;
        }

        $user = Auth::user();
        $query = \Orlando\VoterLoyalty\Models\VoterRecord::query()->with(['leader', 'creator']);

        // 👑 SUPERADMIN / ADMIN / LIDER_ADMIN ven TODO (Agregamos variaciones por si acaso)
        $rolesPoderosos = ['super_admin', 'admin',];

        if (in_array($user->role, $rolesPoderosos)) {
            $query->latest();
        }
        // 👤 Asesor normal
        else {
            $query->where('user_id', $user->id)->latest();
        }

        $voters = $query->paginate($porPagina);
        foreach ($voters->getCollection() as $voter) {
            $voter->acciones = '';
        }

        $posiblesLideres = User::whereIn('role', ['admin'])->get();
        $municipios = \App\Models\Municipio::orderBy('nombre', 'asc')->get();

        return view('voter-loyalty::index', compact('voters', 'posiblesLideres', 'porPagina', 'municipios'));
    }

    /**
     * 4. Guarda un registro de votante completamente nuevo.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'cedula' => 'required|unique:voter_records,cedula',
            'full_name' => 'required|string|max:255',
            'phone' => 'required',
            'fecha_nacimiento' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $voter = new \Orlando\VoterLoyalty\Models\VoterRecord();

        $voter->user_id = Auth::user()->id;
        $voter->creador_original_nombre = Auth::user()->name;
        $voter->cedula = $request->input('cedula');
        $voter->full_name = $request->input('full_name');
        $voter->phone = $request->input('phone');
        $voter->voter_email = $request->input('voter_email');
        $voter->birth_date = $request->input('fecha_nacimiento');

        // Campos Territoriales nuevos
        $voter->city = $request->input('city');
        $voter->neighborhood = $request->input('neighborhood');
        $voter->address = $request->input('address');

        // Campos Laborales nuevos
        $voter->education_level = $request->input('education_level');
        $voter->profession = $request->input('profession');
        $voter->workplace = $request->input('workplace');
        $voter->hire_date = $request->input('hire_date');

        $voter->leader_id = $request->input('leader_id');
        $voter->notes = $request->input('notes');
        $voter->status = $request->input('status');

        $voter->save();

        return redirect()->to('/voters')->with('success', '¡El votante fue registrado con éxito!');
    }

    /**
     * 5. Actualiza un registro existente (Edición).
     */
    public function update(\Illuminate\Http\Request $request, int $id)
    {

        $voter = \Orlando\VoterLoyalty\Models\VoterRecord::findOrFail($id);

        $request->validate([
            'cedula' => 'required|unique:voter_records,cedula,' . $voter->id,
            'full_name' => 'required|string|max:255',
            'phone' => 'required',
            'fecha_nacimiento' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $voter->cedula = $request->input('cedula');
        $voter->full_name = $request->input('full_name');
        $voter->phone = $request->input('phone');
        $voter->voter_email = $request->input('voter_email');
        $voter->birth_date = $request->input('fecha_nacimiento');

        // Actualización Campos Territoriales nuevos
        $voter->city = $request->input('city');
        $voter->neighborhood = $request->input('neighborhood');
        $voter->address = $request->input('address');

        // Actualización Campos Laborales nuevos
        $voter->education_level = $request->input('education_level');
        $voter->profession = $request->input('profession');
        $voter->workplace = $request->input('workplace');
        $voter->hire_date = $request->input('hire_date');

        $voter->leader_id = $request->input('leader_id');
        $voter->notes = $request->input('notes');
        $voter->status = $request->input('status');

        $voter->save();

        return redirect()->to('/voters')->with('success', '¡Los datos se actualizaron con éxito!');
    }

    /**
     * 6. Cierre de sesión.
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/login');
    }

    /**
     * 7. Eliminar un registro individual.
     */
    public function destroy(int $id)
    {
        $rolesPoderosos = ['super_admin', 'admin',];

        if (!in_array(Auth::user()->role, $rolesPoderosos)) {
            return redirect()->to('/voters')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $voter = \Orlando\VoterLoyalty\Models\VoterRecord::findOrFail($id);
        $voter->delete();

        return redirect()->to('/voters')->with('success', '¡El votante fue eliminado con éxito!');
    }

    /**
     * 8. Borrado masivo por checkboxes.
     */
    public function bulkDestroy(\Illuminate\Http\Request $request)
    {
        $rolesPoderosos = ['super_admin', 'admin',];

        if (!in_array(Auth::user()->role, $rolesPoderosos)) {
            return redirect()->to('/voters')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            \Orlando\VoterLoyalty\Models\VoterRecord::whereIn('id', $ids)->delete();
            return redirect()->to('/voters')->with('success', '¡Los votantes seleccionados fueron eliminados con éxito!');
        }

        return redirect()->to('/voters')->with('error', 'No se seleccionó ningún votante.');
    }

    public function bulkReassign(\Illuminate\Http\Request $request)
    {
        // 🛡️ Seguridad: Usamos $request->user() en lugar de auth()->user()
        if ($request->user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Acceso denegado. No tienes permisos para reasignar.');
        }

        $ids = $request->input('ids', []);
        $new_user_id = $request->input('new_user_id');

        if (!empty($ids) && $new_user_id) {
            \Orlando\VoterLoyalty\Models\VoterRecord::whereIn('id', $ids)->update(['user_id' => $new_user_id]);

            return redirect()->back()->with('success', '¡Registros transferidos y reasignados con éxito!');
        }

        return redirect()->back()->with('error', 'No se seleccionaron registros o usuario de destino.');
    }

    /**
     * 9. Valida en tiempo real (AJAX) si una cédula ya existe en la base de datos.
     */
    public function checkCedula(\Illuminate\Http\Request $request)
    {
        $cedula = $request->query('cedula');

        // Verificamos si existe en la base de datos
        $existe = \Orlando\VoterLoyalty\Models\VoterRecord::where('cedula', $cedula)->exists();

        return response()->json(['exists' => $existe]);
    }

    public function clearNote(int $id)
    {
        $voter = \Orlando\VoterLoyalty\Models\VoterRecord::findOrFail($id);
        $voter->notes = null; // Vaciamos la nota
        $voter->save();

        return response()->json(['success' => true]);
    }
}
