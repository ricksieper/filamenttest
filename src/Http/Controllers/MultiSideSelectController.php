<?php

namespace YourVendor\FilamentMultiSideSelect\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Filament\Forms\Get;
use Filament\Forms\ComponentContainer;
use YourVendor\FilamentMultiSideSelect\Components\MultiSideSelect;

class MultiSideSelectController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'state_path' => ['required', 'string'],
            'search' => ['nullable', 'string'],
            'selected' => ['nullable', 'array'],
        ]);
        
        $statePath = $validated['state_path'];
        $search = $validated['search'] ?? null;
        $selected = $validated['selected'] ?? [];
        
        // Get the form component from Livewire
        $livewire = app('filament')->getLivewire(request()->header('X-Livewire-Id'));
        
        if (! $livewire) {
            return response()->json([
                'success' => false,
                'message' => 'Livewire component not found.',
            ], 404);
        }
        
        // Get the form and find the multi-side-select component
        $form = $livewire->getForm();
        $component = $form->getFlatComponents()->first(function ($component) use ($statePath) {
            return $component instanceof MultiSideSelect && $component->getStatePath() === $statePath;
        });
        
        if (! $component) {
            return response()->json([
                'success' => false,
                'message' => 'Component not found.',
            ], 404);
        }
        
        // Get available options based on search
        $availableOptions = $component->getAvailableOptions($search);
        
        return response()->json([
            'success' => true,
            'options' => $availableOptions,
        ]);
    }
}