<?php

namespace App\Providers;

use App\Models\Informe;
use App\Models\Inspeccion;
use App\Models\Dashboard;
use App\Models\Recursos;
use App\Models\Usuario;
use App\Policies\InspeccionPolicy;
use App\Policies\InformePolicy;
use App\Policies\DashboardPolicy;
use App\Policies\RecursosPolicy;
use App\Policies\PersonalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Correspondencia entre el modelo y las políticas de la aplicación.
     */
    protected $policies = [
        Inspeccion::class => InspeccionPolicy::class,
        Informe::class => InformePolicy::class,
        Dashboard::class => DashboardPolicy::class,
        Recursos::class => RecursosPolicy::class,
        Usuario::class => PersonalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
