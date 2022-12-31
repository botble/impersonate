<?php

namespace Botble\Impersonate\Providers;

use Botble\ACL\Models\User;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Impersonate\Guard\SessionGuard;
use Botble\Impersonate\Services\ImpersonateManager;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MacroableModels;

class ImpersonateServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot()
    {
        $this->setNamespace('plugins/impersonate')
            ->loadAndPublishConfigurations(['permissions', 'config'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes();

        $this->app->bind(ImpersonateManager::class, ImpersonateManager::class);

        $this->app->singleton(ImpersonateManager::class, function ($app) {
            return new ImpersonateManager($app);
        });

        $this->app->alias(ImpersonateManager::class, 'impersonate');

        $this->app->booted(function () {

            MacroableModels::addMacro(User::class, 'canImpersonate', function () {
                return true;
            });

            MacroableModels::addMacro(User::class, 'canBeImpersonated', function () {
                return true;
            });

            MacroableModels::addMacro(User::class, 'impersonate', function (Authenticatable $user, ?string $guardName = null) {
                return app(ImpersonateManager::class)->take($this, $user, $guardName);
            });

            MacroableModels::addMacro(User::class, 'isImpersonated', function () {
                return app(ImpersonateManager::class)->isImpersonating();
            });

            MacroableModels::addMacro(User::class, 'leaveImpersonation', function () {
                $impersonateManager = app(ImpersonateManager::class);

                if ($impersonateManager->isImpersonating()) {
                    return $impersonateManager->leave();
                }
            });

        });

        $this->app->register(HookServiceProvider::class);

        Event::listen(Login::class, function () {
            app('impersonate')->clear();
        });

        Event::listen(Logout::class, function () {
            app('impersonate')->clear();
        });

        $this->registerAuthDriver();
    }

    protected function registerAuthDriver(): void
    {
        $auth = $this->app['auth'];

        $auth->extend('session', function (Application $app, $name, array $config) use ($auth) {
            $provider = $auth->createUserProvider($config['provider']);

            $guard = new SessionGuard($name, $provider, $app['session.store']);

            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }
}
