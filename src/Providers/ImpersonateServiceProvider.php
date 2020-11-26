<?php

namespace Botble\Impersonate\Providers;

use Botble\ACL\Models\User;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Lab404\Impersonate\Services\ImpersonateManager;
use MacroableModels;

class ImpersonateServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot()
    {
        $this->setNamespace('plugins/impersonate')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web']);

        $this->app->booted(function () {

            MacroableModels::addMacro(User::class, 'canImpersonate', function () {
                return true;
            });

            MacroableModels::addMacro(User::class, 'canBeImpersonated', function () {
                return true;
            });

            MacroableModels::addMacro(User::class, 'impersonate', function (Model $user, $guardName = null) {
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
    }
}
