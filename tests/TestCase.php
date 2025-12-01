<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate\Tests;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\FilamentPreventOutdatedRecordUpdateServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestbenchTestCase;
use Workbench\App\Models\User;
use Workbench\App\Providers\Filament\AdminPanelProvider;

abstract class TestCase extends OrchestraTestbenchTestCase
{
    use RefreshDatabase, WithWorkbench;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * {@inheritDoc}
     */
    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set('app.key', 'base64:qvko1xdfE5D3y37JXkWlaHIqBU5JuLGQi5bayivUDHk=');
        });
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            FilamentPreventOutdatedRecordUpdateServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            ActionsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            LivewireServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }
}
