<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use App\Policies\EquipoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
         // Equipo::class=>EquipoPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /* Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        }); */
    }
}
