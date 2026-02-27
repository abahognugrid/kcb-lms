<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $sidebar_menuJson = file_get_contents(base_path('resources/menu/sidebar_menu.json'));
    $sidebar_menuData = json_decode($sidebar_menuJson);

    View::composer('layouts.sections.menu.sidebar_menu', function ($view) use ($sidebar_menuData) {
      $view->with('menuData', [$sidebar_menuData])->with('partnerType', auth()->user()->partner?->Access_Type);
    });
  }
}
