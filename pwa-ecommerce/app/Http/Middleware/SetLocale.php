<?php

namespace App\Http\Middleware;

use App\Helpers\LocalizationHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from URL parameter, session, or default
        $locale = $request->get('lang') ??
                  Session::get('locale') ??
                  $request->segment(1);

        // Check if the locale is supported
        if ($locale && LocalizationHelper::isSupportedLocale($locale)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Set default locale if not supported
            $defaultLocale = config('app.locale', 'en');
            App::setLocale($defaultLocale);
            Session::put('locale', $defaultLocale);
        }

        return $next($request);
    }
}
