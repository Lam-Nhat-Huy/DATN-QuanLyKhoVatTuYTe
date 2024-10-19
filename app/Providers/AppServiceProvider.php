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
            // Lấy toàn bộ thông báo
            $getNotifications = Notifications::with('users')
                ->orderBy('created_at', 'DESC')
                ->where('created_at', '>', now()->subDays(7))
                ->where('status', 1)
                ->where('important', 0)
                ->whereNull('deleted_at')
                ->get();


            $data['getNotification'] = $getNotifications;

            // Lấy thông báo quan trọng
            $getImportantNotification = Notifications::with('users')
                ->where('important', 1)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->first();

            $data['getImportantNotification'] = $getImportantNotification;

            // Lấy trạng thái khóa kho khi thấy
            $firstLockWarehouse = Notifications::where('lock_warehouse', 1)
                ->whereNull('deleted_at')
                ->first();

            $data['firstLockWarehouse'] = $firstLockWarehouse->lock_warehouse ?? 2;
        }

        View::share($data);
    }
}
