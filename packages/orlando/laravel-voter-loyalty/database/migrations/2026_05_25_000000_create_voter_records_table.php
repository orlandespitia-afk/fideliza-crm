<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voter_records', function (Blueprint $table) {
            // --- 👤 DATOS IDENTIDAD Y CONTACTO (Campos actuales y correcciones) ---
            $table->id(); // ID único interno
            $table->unsignedBigInteger('user_id')->nullable(); // Guarda el ID del usuario/digitador que creó el registro
            $table->string('cedula')->unique(); // Cédula (no se puede repetir)
            $table->string('full_name'); // Nombre completo
            $table->string('phone')->nullable(); // Teléfono de contacto
            $table->string('voter_email')->nullable(); // Correo del contacto
            $table->date('birth_date')->nullable(); // Para el sistema de alertas de cumpleaños a las 10:00 AM

            // --- 📍 UBICACIÓN GEOGRÁFICA / TERRITORIAL (Para segmentar la tabla) ---
            $table->string('city')->nullable(); // Ciudad o Municipio principal
            $table->string('neighborhood')->nullable(); // Barrio, Comuna, Vereda o Sector
            $table->string('address')->nullable(); // Dirección exacta residencial

            // --- 💼 PERFIL LABORAL Y ESTUDIOS (Para cálculos de antigüedad) ---
            $table->string('education_level')->nullable(); // Nivel de estudio (Bachiller, Técnico, Profesional, etc.)
            $table->string('profession')->nullable(); // Profesión u Oficio actual
            $table->string('workplace')->nullable(); // Lugar de trabajo o Empresa
            $table->date('hire_date')->nullable(); // Fecha de ingreso (calcula días, meses y años automáticamente)

            // --- 📊 ESTRUCTURA POLÍTICA Y ESTADO ---
            // El "Enchufe" de la red: Guarda el ID del líder que lo trajo a la campaña
            $table->unsignedBigInteger('leader_id')->nullable();
            $table->text('notes')->nullable(); // Observaciones o necesidades comunitarias

            // Estado del registro ('active' por defecto para los contadores dinámicos)
            $table->string('status')->default('active');

            $table->timestamps(); // Crea automáticamente 'created_at' y 'updated_at'

            // --- ⛓️ RELACIONES Y LLAVES FORÁNEAS ---
            // Relación con la misma tabla (Estructura de red/jerarquía de líderes)
            $table->foreign('leader_id')->references('id')->on('voter_records')->onDelete('set null');

            // Relación con la tabla de usuarios del sistema (Saber quién digitó el registro)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voter_records');
    }
};
