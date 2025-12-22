<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\View;
use App\Models\Setting;
use App\Models\Menu;

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
    public function boot(): void
    {
        Paginator::useBootstrap();
        // Nếu bạn dùng BS4 thì đổi thành:
        // Paginator::useBootstrapFour();

        View::composer('*', function ($view) {
            $setting = Setting::find(1); // có thể dùng số luôn
            $menu = Menu::orderBy('view', 'asc')->get();

            view()->share([
                'setting' => $setting,
                'menu'    => $menu,
            ]);
        });
    }
}
