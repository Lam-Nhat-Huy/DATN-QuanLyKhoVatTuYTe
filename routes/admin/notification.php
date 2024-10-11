<?php

use App\Http\Controllers\Notification\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;


Route::prefix('notification')->middleware(CheckLogin::class)->group(function () {

    Route::get('/', [NotificationController::class, 'index'])->name('notification.index');

    Route::post('/', [NotificationController::class, 'index'])->name('notification.index');

    Route::get('/notification_trash', [NotificationController::class, 'notification_trash'])->name('notification.notification_trash');

    Route::post('/notification_trash', [NotificationController::class, 'notification_trash'])->name('notification.notification_trash');

    Route::get('/notification_add', [NotificationController::class, 'notification_add'])->name('notification.notification_add');

    Route::post('/notification_create', [NotificationController::class, 'notification_create'])->name('notification.notification_create');

    Route::get('/notification_edit/{code}', [NotificationController::class, 'notification_edit'])->name('notification.notification_edit');

    Route::post('/notification_update/{code}', [NotificationController::class, 'notification_update'])->name('notification.notification_update');

    Route::post('/create_notification_type', [NotificationController::class, 'create_notification_type'])->name('notification.create_notification_type');

    Route::post('/delete_notification_type/{id}', [NotificationController::class, 'delete_notification_type'])->name('notification.delete_notification_type');

    Route::get('notifications/count', [NotificationController::class, 'getNewNotificationCount'])->name('notifications.count');

    Route::get('/notifications/markAsRead', [NotificationController::class, 'markNotificationsAsRead'])->name('notifications.markAsRead');
});