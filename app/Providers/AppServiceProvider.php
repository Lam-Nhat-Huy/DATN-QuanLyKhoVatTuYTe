<?php

namespace App\Providers;

use App\Models\Notifications;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $data = [];

        if (Schema::hasTable('notifications')) {
            $getNotifications = Notifications::with('users')
                ->orderBy('created_at', 'DESC')
                ->where('created_at', '>', now()->subDays(7))
                ->whereIn('status', [1, 2]) // Sử dụng whereIn để lọc theo nhiều giá trị
                ->whereNull('deleted_at')
                ->get();


            $data['getNotification'] = $getNotifications;

            $firstLockWarehouse = Notifications::where('lock_warehouse', 1)
                ->whereNull('deleted_at')
                ->first();

            $data['firstLockWarehouse'] = $firstLockWarehouse->lock_warehouse ?? 2;
        }

        View::share($data);
    }
}