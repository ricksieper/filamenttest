<?php

namespace rlessi\FilamentMultiSideSelect\Facades;

use Illuminate\Support\Facades\Facade;
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect as MultiSideSelectComponent;

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