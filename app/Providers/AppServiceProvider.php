<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $currentUser = null;
            static $fetched = false;

            if (session()->has('user_id')) {
                if (!$fetched) {
                    $currentUser = \App\Models\User::find(session('user_id'));
                    $fetched = true;
                }

                if ($currentUser) {
                    $view->with('currentUser', $currentUser);
                }
            }
        });
    }
}
