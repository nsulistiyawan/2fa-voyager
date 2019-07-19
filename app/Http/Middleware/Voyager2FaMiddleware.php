<?php

namespace App\Http\Middleware;

use Closure;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;

class Voyager2FaMiddleware extends VoyagerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app('VoyagerAuth')->guest()) {
            $user = app('VoyagerAuth')->user();
            if(empty($user->token_2fa)){
                return redirect()->route('admin.setup-2fa');
            }

            $otpVerified = $request->session()->has('2fa-verified') && $request->session()->get('2fa-verified');
            if(!$otpVerified){
                return redirect()->route('admin.verify-2fa');
            }

            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        $urlLogin = route('voyager.login');

        return redirect()->guest($urlLogin);
    }
}