<?php

namespace Orlando\VoterLoyalty\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class VoterRecord extends Model
{
    // Tu tabla real reconstruida en el paso anterior
    protected $table = 'voter_records';

    // 🔒 Lista blanca de campos: Habilitamos los campos anteriores y los nuevos para la Magia de UX
    protected $fillable = [
        'user_id',
        'cedula',
        'full_name',
        'phone',
        'voter_email',
        'birth_date',
        'leader_id',
        'notes',

        // 📍 Nuevos Campos de Ubicación Geográfica
        'city',
        'neighborhood',
        'address', // Ahora actúa estrictamente como dirección exacta residencial

        // 💼 Nuevos Campos del Perfil Laboral y de Estudio
        'education_level',
        'profession',
        'workplace',
        'hire_date',

        // 🟢 Estado en la campaña
        'status'
    ];

    // 🗓️ Casteo de Fechas: Le ordenamos a Laravel que maneje estos campos como objetos Carbon verdaderos
    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    /**
     * Relación con el Usuario/Digitador del sistema que creó el registro (La que ya habíamos cuadrado)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el Líder Político (Estructura de red/jerarquía)
     */
    public function leader()
    {
        return $this->belongsTo(self::class, 'leader_id');
    }

    /**
     * 🧠 LA MAGIA DEL BACKEND: Cálculo de antigüedad laboral automático en tiempo real
     * Cada vez que en la vista llames a $voter->antiguedad_laboral, se ejecutará esta lógica
     * restando la fecha de ingreso con el día de hoy exacto.
     */
    public function getAntiguedadLaboralAttribute(): string
    {
        if (!$this->hire_date) {
            return 'No registrada';
        }

        $fechaIngreso = $this->hire_date;
        $hoy = Carbon::now();

        // Control de errores si digitan una fecha futura
        if ($fechaIngreso->isAfter($hoy)) {
            return '0 días';
        }

        // Calcula la diferencia exacta desglosada
        $diff = $fechaIngreso->diff($hoy);

        // Armamos un texto limpio, condicionado al singular/plural en español
        $resultado = [];
        if ($diff->y > 0) {
            $resultado[] = $diff->y . ($diff->y === 1 ? ' año' : ' años');
        }
        if ($diff->m > 0) {
            $resultado[] = $diff->m . ($diff->m === 1 ? ' mes' : ' meses');
        }
        if ($diff->d > 0) {
            $resultado[] = $diff->d . ($diff->d === 1 ? ' día' : ' días');
        }

        // Retorna un texto elegante como: "2 años, 5 meses, 3 días"
        return count($resultado) > 0 ? implode(', ', $resultado) : 'Ingresó hoy';
    }
}
