<?php

namespace App\Providers;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        //Obtenemos las notificaciones de manera global
        app()->singleton('notifications', function() {
            $userCurrent = Auth::user()->id;
            $users = User::where('user_id',$userCurrent)->get();
            $userIds = $users->pluck('id')->toArray();

            return Notification::whereIn('user_id', $userIds)
                              ->where('status', false)
                              ->with('user')
                              ->get();
        });
    }
}
