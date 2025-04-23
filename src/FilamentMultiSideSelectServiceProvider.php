<?php

namespace YourVendor\FilamentMultiSideSelect;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMultiSideSelectServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-multi-side-select';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasTranslations()
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations();
            });

        $configFileName = $package->shortName();

        $this->publishes([
            __DIR__ . "/../resources/dist/css/{$configFileName}.css" => public_path("vendor/{$package->shortName()}/{$configFileName}.css"),
            __DIR__ . "/../resources/dist/js/{$configFileName}.js" => public_path("vendor/{$package->shortName()}/{$configFileName}.js"),
        ], "{$package->shortName()}-assets");
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register([
            Css::make(static::$name, __DIR__ . '/../resources/dist/css/filament-multi-side-select.css'),
            Js::make(static::$name, __DIR__ . '/../resources/dist/js/filament-multi-side-select.js'),
            AlpineComponent::make('filament-multi-side-select', __DIR__ . '/../resources/dist/components/multi-side-select.js'),
        ], 'your-vendor/filament-multi-side-select');

        // Icon Registration
        FilamentIcon::register([
            'multi-side-select' => 'heroicon-o-arrows-right-left',
        ]);
    }
}