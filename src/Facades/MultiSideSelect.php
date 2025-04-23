<?php

namespace YourVendor\FilamentMultiSideSelect\Facades;

use Illuminate\Support\Facades\Facade;
use YourVendor\FilamentMultiSideSelect\Components\MultiSideSelect as MultiSideSelectComponent;

/**
 * @method static MultiSideSelectComponent make(string $name)
 * 
 * @see MultiSideSelectComponent
 */
class MultiSideSelect extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-multi-side-select';
    }
    
    public static function make(string $name): MultiSideSelectComponent
    {
        return app(MultiSideSelectComponent::class, ['name' => $name]);
    }
}