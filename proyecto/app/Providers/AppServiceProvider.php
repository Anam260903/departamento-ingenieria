<?php

namespace App\Providers;

// Importar los modelos y la nueva política
use App\Models\Informe;
use App\Models\Inspeccion;
use App\Models\Dashboard;
use App\Policies\InspeccionPolicy;
use App\Policies\InformePolicy;
use App\Policies\DashboardPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Correspondencia entre el modelo y las políticas de la aplicación.
     */
    protected $policies = [
        // Mapeo de la política al modelo de inspección
        Inspeccion::class => InspeccionPolicy::class,
        Informe::class => InformePolicy::class,
        Dashboard::class => DashboardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
