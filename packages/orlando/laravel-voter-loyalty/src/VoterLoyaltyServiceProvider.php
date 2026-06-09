<?php

namespace Orlando\VoterLoyalty;

use Illuminate\Support\ServiceProvider;

class VoterLoyaltyServiceProvider extends ServiceProvider
{
    /**
     * Se ejecuta cuando Laravel arranca y carga todos los paquetes.
     */
    public function register()
    {
        //
    }

    /**
     * Se ejecuta cuando el paquete ya está listo para usarse.
     */
    public function boot()
    {
        // 1. Cargar las rutas desde la raíz de src
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // 2. CORREGIDO: Tus vistas reales están en src/views (según tu captura)
        $this->loadViewsFrom(__DIR__ . '/views', 'voter-loyalty');

        // 3. Corregir la ruta de las migraciones si están dentro de src (si no, déjala igual)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
