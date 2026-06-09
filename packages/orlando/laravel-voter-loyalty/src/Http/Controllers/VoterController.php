<?php

namespace Orlando\VoterLoyalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Orlando\VoterLoyalty\Models\VoterRecord;
use Illuminate\Support\Facades\Auth; // <-- Este es el escudo que lee la herramienta correctamente

class VoterController extends Controller
{
    /**
     * Muestra la pantalla principal con el listado de votantes en español.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Capturamos cuántas filas quiere ver el usuario (por defecto 10)
        $porPagina = $request->input('per_page', 10);

        // Validamos que solo acepte números permitidos por seguridad
        if (!in_array($porPagina, [10, 30, 50])) {
            $porPagina = 10;
        }

        // 2. Traemos los registros PAGINADOS directamente desde tu modelo de paquete
        $voters = \Orlando\VoterLoyalty\Models\VoterRecord::latest()->paginate($porPagina);

        // 3. 🟢 MAPEO AL ESPAÑOL INTELIGENTE:
        // Como es un objeto paginado, usamos ->getCollection() para poder recorrerlos 
        // y renombrar las variables igual que lo tenías antes, sin romper la paginación.
        foreach ($voters->getCollection() as $voter) {
            $voter->email = $voter->voter_email;
            $voter->fecha_nacimiento = $voter->birth_date;
            $voter->notas            = $voter->notes;
        }

        // 4. Mantenemos el parámetro 'per_page' en la URL al cambiar de página
        $voters->appends(['per_page' => $porPagina]);

        // 5. Cargamos tu plantilla del paquete enviándole los datos y la cantidad elegida
        return view('voter-loyalty::index', compact('voters', 'porPagina'));
    }

    /**
     * Guarda un nuevo votante aplicando las reglas obligatorias y opcionales.
     */

    public function store(\Illuminate\Http\Request $request)
    {
        // 1. Validamos los campos que vienen de la pantalla
        $request->validate([
            'cedula'           => 'required|unique:voter_records,cedula',
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string',
            'voter_email'      => 'nullable|email',
            'address'          => 'nullable|string',
            'fecha_nacimiento' => 'required|date',
            'leader_id'        => 'nullable|integer',
            'notas'            => 'nullable|string',
        ]);

        // 2. Creamos el objeto para la base de datos
        $voter = new \Orlando\VoterLoyalty\Models\VoterRecord();
        $voter->cedula       = $request->input('cedula');
        $voter->full_name    = $request->input('full_name');
        $voter->phone        = $request->input('phone');
        $voter->address      = $request->input('address');
        $voter->voter_email  = $request->input('voter_email');

        // 🟢 MAPEAMOS AL INGLÉS: Guardamos en las columnas reales de tu MySQL
        $voter->birth_date   = $request->input('fecha_nacimiento');
        $voter->leader_id    = $request->input('leader_id');
        $voter->notes        = $request->input('notas');

        // 3. Guardamos físicamente en XAMPP
        $voter->save();

        // 4. Redireccionamos con mensaje de éxito
        return redirect()->to('/voters')->with('success', '¡Votante registrado con éxito!');
    }

    /**
     * Muestra la pantalla visual del Login (Formulario en blanco).
     */
    public function showLoginForm()
    {
        return view('voter-loyalty::login');
    }

    /**
     * Procesa el formulario de ingreso y valida las credenciales en XAMPP.
     */
    public function login(\Illuminate\Http\Request $request)
    {
        // Validamos texto normal para el usuario
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Intentamos iniciar sesión con el campo username en XAMPP
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('voters.index');
        }

        // Si falla, el error ahora rastrea el campo username
        return back()->withErrors([
            'username' => 'El nombre de usuario o la contraseña no coinciden.',
        ]);
    }

    /**
     * Procesa los cambios enviados desde el modal de edición (PUT/PATCH).
     */
    public function update(\Illuminate\Http\Request $request, int $id)
    {
        // 1. Buscamos el votante real en la base de datos
        $voter = \Orlando\VoterLoyalty\Models\VoterRecord::findOrFail($id);

        // 2. Validamos los datos incluyendo el líder y los nuevos campos
        $request->validate([
            'cedula'           => 'required|unique:voter_records,cedula,' . $voter->id,
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string',
            'voter_email'      => 'nullable|email',
            'address'          => 'nullable|string',
            'fecha_nacimiento' => 'required|date',
            'leader_id'        => 'nullable|integer',
            'notas'            => 'nullable|string',
        ]);

        // 3. Asignamos los datos traduciendo al inglés para tu base de datos de MySQL
        $voter->cedula       = $request->input('cedula');
        $voter->full_name    = $request->input('full_name');
        $voter->phone        = $request->input('phone');
        $voter->address      = $request->input('address');
        $voter->voter_email  = $request->input('voter_email');

        // 🟢 MAPEAMOS AL INGLÉS: Actualizamos apuntando a las columnas reales de tu tabla
        $voter->birth_date   = $request->input('fecha_nacimiento');
        $voter->leader_id    = $request->input('leader_id'); // <-- ¡Aquí capturamos el ID del líder seleccionado!
        $voter->notes        = $request->input('notas');

        // 4. Guardamos los cambios físicamente en XAMPP
        $voter->save();

        // 5. Redireccionamos limpiamente con mensaje de éxito
        return redirect()->to('/voters')->with('success', '¡Los datos se actualizaron con éxito!');
    }
    /**
     * Cierra la sesión de forma segura.
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirecciona por URL directa
        return redirect()->to('/login');
    }

    /**
     * 🟢 NUEVO: Elimina un solo votante de la base de datos.
     */
    public function destroy(int $id)
    {
        // Usamos la ruta completa de Auth para que XAMPP no te bote error
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            return redirect()->to('/voters')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $voter = \Orlando\VoterLoyalty\Models\VoterRecord::findOrFail($id);
        $voter->delete();

        return redirect()->to('/voters')->with('success', '¡El votante fue eliminado con éxito!');
    }

    /**
     * 🟢 NUEVO: Elimina múltiples votantes seleccionados por lotes.
     */
    public function bulkDestroy(\Illuminate\Http\Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            return redirect()->to('/voters')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        // Recibimos la lista de IDs seleccionados desde los checkboxes
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            \Orlando\VoterLoyalty\Models\VoterRecord::whereIn('id', $ids)->delete();
            return redirect()->to('/voters')->with('success', '¡Los votantes seleccionados fueron eliminados con éxito!');
        }

        return redirect()->to('/voters')->with('error', 'No seleccionaste ningún votante.');
    }
}
