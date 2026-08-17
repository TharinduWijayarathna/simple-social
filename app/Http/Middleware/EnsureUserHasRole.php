<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $allowed = collect($roles)->map(fn (string $role): string => Role::from($role)->value);

        abort_unless($allowed->contains($user->role->value), 403);

        return $next($request);
    }
}
