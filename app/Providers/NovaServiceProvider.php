<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Illuminate\Http\Request;
use App\Nova\Dashboards\Main;
use App\Nova\Dashboards\TwoDAnalytics;
use App\Nova\Dashboards\BetAnalytics;
use App\Nova\Dashboards\UserAnalytics;
use App\Nova\User;
use App\Nova\Admin;
use App\Nova\Ads;
use App\Nova\Holidays;
use App\Nova\VoucherClose;
use App\Nova\VoucherOpen;
use App\Nova\Voucher;
class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::mainMenu(
            function(Request $request){
                return[
                    MenuSection::dashboard(Main::class)->icon('chart-bar'),
                    MenuSection::make("Analystics",[
                        MenuItem::dashboard(TwoDAnalytics::class),
                        MenuItem::dashboard(UserAnalytics::class),
                        MenuItem::dashboard(BetAnalytics::class),
                        // MenuItem::dashboard(GoogleAnalytics::class),
                    ])->icon('chart-bar')->collapsable(),
                    MenuSection::make('Vouchers',[
                        MenuItem::resource(Voucher::class),
                        MenuItem::resource(VoucherOpen::class),
                        MenuItem::resource(VoucherClose::class),
                        // MenuItem::resource(BanList::class),
                    ])->icon('document-text')->collapsable(),
                     MenuSection::make('Users',[
                        MenuItem::resource(User::class),
                        MenuItem::resource(Admin::class),
                        // MenuItem::resource(BanList::class),
                    ])->icon('user')->collapsable(),
                      MenuSection::make('Utils',[
                        MenuItem::resource(Ads::class),
                    ])->icon('user')->collapsable(),
                ];
            }
        );
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return auth()->check();
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
              new \App\Nova\Dashboards\Main,
              new \App\Nova\Dashboards\TwoDAnalytics,
              new \App\Nova\Dashboards\BetAnalytics,
              new \App\Nova\Dashboards\UserAnalytics,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        // return [];
        return [
            // ...
            // new \PhpJunior\NovaLogViewer\Tool(),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}