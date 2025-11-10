<?php

namespace App\Providers;

// Importar los modelos y la nueva política
use App\Models\Informe;
use App\Models\Inspeccion;
use App\Policies\InspeccionPolicy;
use App\Policies\InformePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
            // Mapeo de la Política al Modelo de Inspección
        Inspeccion::class => InspeccionPolicy::class,
        Informe::class => InformePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
