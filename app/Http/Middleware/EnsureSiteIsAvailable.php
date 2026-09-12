<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsAvailable
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isUnderConstruction = Setting::get('site_under_construction', '0') === '1';

        if (! $isUnderConstruction || $request->user()?->isSuperAdmin() || $request->is('admin*')) {
            return $next($request);
        }

        return response()
            ->view('under-construction', [
                'title' => Setting::get('under_construction_title', 'We are improving VibeCraft'),
                'message' => Setting::get('under_construction_message', 'The platform will be back shortly. Thank you for your patience.'),
                'supportEmail' => Setting::get('support_email'),
            ], Response::HTTP_SERVICE_UNAVAILABLE)
            ->header('Retry-After', '3600');

    }
}
