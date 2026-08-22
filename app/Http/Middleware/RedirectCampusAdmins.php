<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectCampusAdmins
{
    /**
     * Redirect campus admins away from the student-facing social routes
     * to their dedicated panel. Campus admins are a separate account type
     * and must not access the social media side of the app.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->role === Role::CampusAdmin) {
            return redirect()->route('campus.dashboard');
        }

        return $next($request);
    }
}
