<?php

namespace App\Providers;

use App\Models\Loanrequest;
use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
            // Register the View Composer
    View::composer('layouts.app', function ($view) {
        $pendReq = Loanrequest::where('approval', 'pending')->get();
        $count_req = count($pendReq);
        
        $notifications = Notification::whereNull('read_at')->get();
        $count_not = count($notifications);
        
        $view->with(['count_not' => $count_not, 'count_req' => $count_req]);
    });
    }
}
