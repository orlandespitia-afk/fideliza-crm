<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voter_records', function (Blueprint $table) {
            $table->id(); // ID único interno
            $table->string('cedula')->unique(); // Cédula (no se puede repetir)
            $table->string('full_name'); // Nombre completo
            $table->string('phone')->nullable(); // Teléfono de contacto
            $table->string('voter_email')->nullable(); // Correo del contacto
            $table->string('address')->nullable(); // Dirección / Barrio / Comuna
            $table->date('birth_date')->nullable(); // Para el sistema de alertas de cumpleaños

            // El "Enchufe" de la red: Guarda el ID del líder que lo trajo a la campaña
            $table->unsignedBigInteger('leader_id')->nullable();

            $table->text('notes')->nullable(); // Observaciones o necesidades comunitarias
            $table->timestamps(); // Crea automáticamente las columnas 'created_at' y 'updated_at'

            // Llave foránea: hace que un registro apunte a otro registro de la misma tabla (la red)
            $table->foreign('leader_id')->references('id')->on('voter_records')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voter_records');
    }
};
