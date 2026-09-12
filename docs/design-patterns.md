# Design Patterns

## MVC Architecture

**Model** — Eloquent models such as `User`, `PortfolioItem`, `Status`, and `Collaboration` represent the application's data and encapsulate relationships, scopes, and query logic. For example, `PortfolioItem` defines its relationships to `User`, `Like`, and `Comment`, and exposes helper methods like `isPublished()`.

**View** — Blade templates render the UI and display data passed down to them. For example, `resources/views/livewire/portfolio/show.blade.php` and `resources/views/livewire/portfolio/index.blade.php` render a student's portfolio items without containing any business logic themselves.

**Controller** — For the JSON API, controllers under `app/Http/Controllers/Api/V1` (e.g. `LikeController`, `FollowController`, `EventController`) receive HTTP requests, authorize them, delegate the real work to an Action or the Model, and return a response. For the web UI, this role is filled by Livewire components (e.g. `app/Livewire/Portfolio/Index.php`), which handle user interaction and pass state to their paired Blade view.

**Code reference:**
`app/Http/Controllers/Api/V1/LikeController.php:11-24`
```php
class LikeController extends Controller
{
    public function store(Request $request, PortfolioItem $portfolioItem, ToggleLike $toggleLike): JsonResponse
    {
        $this->authorize('view', $portfolioItem);

        $result = $toggleLike->handle($request->user(), $portfolioItem);

        return response()->json([
            'liked' => $result['liked'],
            'likes_count' => $portfolioItem->likes()->count(),
        ]);
    }
}
```

---

## 1. Action Pattern

Instead of writing business logic directly inside controllers, each unit of behaviour (liking a post, following a user, awarding XP) is extracted into its own single-purpose class under `app/Actions`, each exposing a `handle()` method. Controllers stay thin and simply call the action, making the logic reusable, testable, and easy to locate. This keeps controllers focused only on HTTP concerns.

**Code reference:**
`app/Actions/ToggleLike.php:11-24`
```php
class ToggleLike
{
    public function __construct(private AwardXp $awardXp) {}

    public function handle(User $user, PortfolioItem $portfolioItem): array
    {
        $existing = Like::query()
            ->whereBelongsTo($user)
            ->whereMorphedTo('likeable', $portfolioItem)
            ->first();

        if ($existing !== null) {
            $existing->delete();
            return ['liked' => false, 'like' => null];
        }
        // ...
    }
}
```

---

## 2. Policy (Authorization Strategy) Pattern

Authorization rules for each model are isolated into a dedicated Policy class rather than scattered across controllers with if-statements. Each policy defines who can view, create, update, or delete a given resource, and Laravel automatically resolves the right policy for a model. This centralizes access rules and keeps them consistent across every entry point (web and API).

**Code reference:**
`app/Policies/PortfolioItemPolicy.php:15-32`
```php
public function view(?User $user, PortfolioItem $portfolioItem): bool
{
    if ($portfolioItem->isPublished()) {
        return true;
    }
    return $user !== null && ($user->is($portfolioItem->user) || $user->isAdmin());
}

public function update(User $user, PortfolioItem $portfolioItem): bool
{
    return $user->is($portfolioItem->user) || $user->isAdmin();
}
```

---

## 3. Middleware (Chain of Responsibility) Pattern

Cross-cutting request checks, such as role-based access control, are implemented as middleware that sit between the incoming request and the route handler. `EnsureUserHasRole` inspects the authenticated user's role against an allowed list before passing the request further down the chain, aborting early if the check fails. This keeps role checks reusable across many routes without duplicating logic in each controller.

**Code reference:**
`app/Http/Middleware/EnsureUserHasRole.php:15-26`
```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();
    abort_unless($user !== null, 401);

    $allowed = collect($roles)->map(fn (string $role): string => Role::from($role)->value);
    abort_unless($allowed->contains($user->role->value), 403);

    return $next($request);
}
```

---

## 4. Trait (Mixin) Pattern with Global Scope

Shared, cross-model behaviour — restricting records to a user's campus — is implemented once in `HasCampusScope` and mixed into multiple models (`User`, `Event`, `PortfolioItem`, `Status`, `Collaboration`) instead of duplicating the same scoping logic in each. The trait's boot method registers a global query scope so campus filtering is applied automatically to every query on those models, guarded against infinite recursion with `CampusScopeGuard`.

**Code reference:**
`app/Traits/HasCampusScope.php:19-27`
```php
public static function bootHasCampusScope(): void
{
    static::addGlobalScope('campus_scope', function (Builder $builder) {
        if (CampusScopeGuard::$active) {
            return;
        }
        CampusScopeGuard::$active = true;
        // ... applies campus_id filtering per model
    });
}
```

---

## 5. Service Provider (Bootstrapper) Pattern

Application-wide configuration — custom authentication guards, rate limiters, and framework defaults — is centralized in `AppServiceProvider` rather than being configured ad hoc across the codebase. The `boot()` method wires up a custom `device-token` auth driver for wearable devices and named rate limiters (`api`, `auth`, `wearable`) that routes can reference by name, giving the app one authoritative place to bootstrap cross-cutting services.

**Code reference:**
`app/Providers/AppServiceProvider.php:90-103`
```php
protected function configureRateLimiting(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('auth', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
}
```
