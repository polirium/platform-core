<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Polirium\Core\Base\Http\Models\User;

class FortifyServiceProvider extends ServiceProvider
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
        // Fortify::createUsersUsing(CreateNewUser::class);
        // Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        // Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->orWhere('username', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        $this->registerRateLimit();
        $this->registerViews();
    }

    public function registerViews(): void
    {
        Fortify::loginView(function () {
            return view('core/base::auth.login');
        });
    }

    public function registerRateLimit(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return $this->limitLogin($request);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    protected function limitLogin(Request $request): Limit
    {
        return Limit::perMinute(5)->by($this->key($request));
    }

    protected function key(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
    }
}
