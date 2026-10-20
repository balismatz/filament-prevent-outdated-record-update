<?php

namespace Workbench\App\Providers;

use Filament\Actions\EditAction;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        EditAction::configureUsing(function (EditAction $action): EditAction {
            if ($action->getName() !== 'edit-allow-outdated') {
                return $action;
            }

            return $action->preventOutdatedRecordUpdate();
        });
    }
}
