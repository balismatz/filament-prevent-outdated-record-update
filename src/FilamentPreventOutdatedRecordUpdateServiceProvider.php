<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate;

use Filament\Actions\EditAction;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;

class FilamentPreventOutdatedRecordUpdateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'filament-prevent-outdated-record-update');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/filament-prevent-outdated-record-update'),
        ]);

        EditAction::macro('preventOutdatedRecordUpdate', function (): EditAction {
            /** @var \Filament\Actions\EditAction $this */
            return $this->beforeFormValidated(
                function (EditAction $action, Component $livewire, Model $record) {
                    $data = collect($livewire->mountedActions ?? [])
                        ->pluck('data')
                        ->last() ?: [];

                    try {
                        (new PreventOutdatedRecordUpdate($data, $record))();
                    } catch (Halt) {
                        // Call the "halt" method to make sure that prevention
                        // will always be done correctly.
                        $action->halt();
                    }

                    return $action->callBeforeFormValidated();
                }
            );
        });
    }
}
