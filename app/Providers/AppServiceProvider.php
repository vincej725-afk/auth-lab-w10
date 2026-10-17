<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // A gate is a named yes/no rule that is not tied to one model
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
    }
}
