<?php

namespace Orlando\VoterLoyalty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoterRecord extends Model
{
    // Vinculamos el modelo con la tabla real de XAMPP
    protected $table = 'voter_records';

    // Campos permitidos para llenarse desde formularios
    protected $fillable = [
        'cedula',
        'full_name',
        'phone',
        'voter_email',
        'address',
        'birth_date',
        'leader_id',
        'notes',
    ];

    /**
     * RELACIÓN 1: El Líder
     * Un votante pertenece a un líder que lo recomendó.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(VoterRecord::class, 'leader_id');
    }

    /**
     * RELACIÓN 2: Los Referidos (Las Ramitas)
     * Un líder puede tener asignados muchos votantes en su red.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(VoterRecord::class, 'leader_id');
    }
}
