<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="multiSideSelectComponent({
            state: $wire.entangle('{{ $getStatePath() }}'),
            statePath: '{{ $getStatePath() }}',
            searchable: {{ $isSearchable() ? 'true' : 'false' }},
            displayAttribute: '{{ $getDisplayAttribute() }}',
            titleAttribute: '{{ $getTitleAttribute() }}',
            itemsPerPage: {{ $getItemsPerPage() }},
            selectedOptions: {{ json_encode($getSelectedOptions()) }},
            availableOptions: {{ json_encode($getAvailableOptions()) }},
        })"
        wire:ignore
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->class(['filament-forms-multi-side-select-component space-y-2'])
        }}
    >
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
            <!-- Available Options Panel -->
            <div class="w-full md:w-1/2 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">{{ __('Available Options') }}</h3>
                    
                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            x-on:click="selectAll"
                            class="text-xs text-primary-600 hover:text-primary-500 font-medium"
                        >
                            {{ __('Select All') }}
                        </button>
                    </div>
                </div>
                
                <div class="rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        <div class="flex items-center space-x-1">
                            <span class="flex items-center justify-center w-5 h-5">
                                <input
                                    type="checkbox"
                                    x-model="allAvailableSelected"
                                    x-on:change="toggleAllAvailable"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-primary-600 dark:focus:ring-primary-600"
                                />
                            </span>
                            
                            <div class="flex-1">
                                <input
                                    type="search"
                                    x-model.debounce.500ms="search"
                                    x-on:keydown.enter.prevent=""
                                    placeholder="{{ __('Search...') }}"
                                    class="block w-full border-0 px-3 py-1 placeholder-gray-400 shadow-sm focus:ring-0 sm:text-sm dark:bg-gray-800 dark:placeholder-gray-500 dark:text-white"
                                    x-bind:class="{ 'hidden': ! {{ $isSearchable() ? 'true' : 'false' }} }"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-72 overflow-y-auto">
                        <div
                            x-show="filteredAvailableOptions.length === 0 && search.length > 0"
                            class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ __('No results found.') }}
                        </div>
                        
                        <template x-for="(option, index) in filteredAvailableOptions" :key="index">
                            <div class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <span class="flex items-center justify-center w-5 h-5">
                                    <input
                                        type="checkbox"
                                        x-model="availableSelected"
                                        :value="index"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-primary-600 dark:focus:ring-primary-600"
                                    />
                                </span>
                                
                                <span class="text-sm" x-text="option.label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Move Buttons -->
            <div class="flex md:flex-col items-center justify-center md:justify-center space-x-2 md:space-x-0 md:space-y-2">
                <button
                    type="button"
                    x-on:click="moveToSelected"
                    x-bind:disabled="availableSelected.length === 0"
                    class="flex items-center justify-center p-1 rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 hover:text-primary-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
                
                <button
                    type="button"
                    x-on:click="moveToAvailable"
                    x-bind:disabled="selectedSelected.length === 0"
                    class="flex items-center justify-center p-1 rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 hover:text-primary-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                </button>
            </div>
            
            <!-- Selected Options Panel -->
            <div class="w-full md:w-1/2 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">{{ __('Selected Options') }}</h3>
                    
                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            x-on:click="deselectAll"
                            class="text-xs text-primary-600 hover:text-primary-500 font-medium"
                        >
                            {{ __('Clear All') }}
                        </button>
                    </div>
                </div>
                
                <div class="rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        <div class="flex items-center space-x-1">
                            <span class="flex items-center justify-center w-5 h-5">
                                <input
                                    type="checkbox"
                                    x-model="allSelectedSelected"
                                    x-on:change="toggleAllSelected"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-primary-600 dark:focus:ring-primary-600"
                                />
                            </span>
                            
                            <div class="flex-1">
                                <div class="px-3 py-1 text-sm">
                                    <span x-text="selectedOptionsCount"></span> {{ __('selected') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-72 overflow-y-auto">
                        <div
                            x-show="selectedOptionsArray.length === 0"
                            class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ __('No options selected.') }}
                        </div>
                        
                        <template x-for="(option, index) in selectedOptionsArray" :key="index">
                            <div 
                                class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                            >
                                <span class="flex items-center justify-center w-5 h-5">
                                    <input
                                        type="checkbox"
                                        x-model="selectedSelected"
                                        :value="option.key"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-primary-600 dark:focus:ring-primary-600"
                                    />
                                </span>
                                
                                <span class="text-sm" x-text="option.label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" x-bind:value="JSON.stringify(state)" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />
    </div>
</x-dynamic-component>