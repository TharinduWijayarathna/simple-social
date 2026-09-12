<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\Setting;
use App\Support\MailSettings;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->configureAuth();
        $this->configureRateLimiting();
        $this->configureStoredMailSettings();
    }

    protected function configureStoredMailSettings(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $siteName = Setting::get('site_name');

                if (filled($siteName)) {
                    config(['app.name' => $siteName]);
                }

                if (filled(Setting::get('smtp_host'))) {
                    app(MailSettings::class)->apply();
                }
            }
        } catch (\Throwable) {
            // The application must remain bootable while its database is unavailable or migrating.
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Model::preventLazyLoading(app()->isLocal());

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuth(): void
    {
        Auth::viaRequest('device-token', function (Request $request): mixed {
            $bearer = $request->bearerToken();

            if ($bearer === null || ! str_contains($bearer, '|')) {
                return null;
            }

            [$id, $plainTextToken] = explode('|', $bearer, 2);

            $device = Device::query()
                ->active()
                ->with('user')
                ->find($id);

            if ($device === null || ! hash_equals($device->token_hash, hash('sha256', $plainTextToken))) {
                return null;
            }

            Context::addHidden('device', $device);

            defer(fn () => $device->touchLastSeen());

            return $device->user;
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('wearable', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
