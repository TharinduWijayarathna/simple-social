<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonStudents
{
    /**
     * Redirect campus admins and super admins away from the student-facing
     * social routes to their dedicated dashboards.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            if ($user->role === Role::CampusAdmin) {
                return redirect()->route('campus.dashboard');
            }

            if ($user->role === Role::SuperAdmin) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
