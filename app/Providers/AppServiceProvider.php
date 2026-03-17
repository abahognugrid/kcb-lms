<?php

namespace App\Providers;

use App\Contracts\SmsProviderInterface;
use App\Notifications\Channels\DatabaseChannel;
use App\Notifications\SmsNotification;
use App\Services\Sms;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProviderInterface::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Register the custom SMS driver
        Notification::extend('sms', function ($app) {
            // Return a closure that will create the Sms instance when needed
            return new class
            {
                public function send($notifiable, SmsNotification $notification)
                {
                    $smsService = new Sms($notification);
                    $smsService->send($notifiable, $notification);
                }
            };
        });

        $this->app->instance(BaseDatabaseChannel::class, new DatabaseChannel);
    }
}
