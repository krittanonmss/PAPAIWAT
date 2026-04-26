<?php

namespace App\Providers;

use App\View\Composers\FrontendMenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('frontend.partials.navigation', FrontendMenuComposer::class);
    }
}