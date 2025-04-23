import { debounce } from '../utils/debounce.js';

export default function multiSideSelectComponent(config) {
    return {
        state: config.state || [],
        statePath: config.statePath,
        searchable: config.searchable || false,
        displayAttribute: config.displayAttribute,
        titleAttribute: config.titleAttribute || config.displayAttribute,
        itemsPerPage: config.itemsPerPage || 10,
        initialSelectedOptions: config.selectedOptions || {},
        initialAvailableOptions: config.availableOptions || {},
        
        search: '',
        availableOptions: [],
        selectedOptions: {},
        availableSelected: [],
        selectedSelected: [],
        allAvailableSelected: false,
        allSelectedSelected: false,
        loading: false,
        
        init() {
            this.availableOptions = Object.entries(this.initialAvailableOptions).map(([key, label]) => ({
                key,
                label
            }));
            
            this.selectedOptions = this.initialSelectedOptions;
            this.updateSelectedOptionsArray();
            
            this.$watch('search', debounce(() => {
                this.searchOptions();
            }, 500));
            
            this.$watch('availableSelected', () => {
                this.updateAllAvailableSelected();
            });
            
            this.$watch('selectedSelected', () => {
                this.updateAllSelectedSelected();
            });
        },
        
        get selectedOptionsArray() {
            return Object.entries(this.selectedOptions).map(([key, label]) => ({
                key,
                label
            }));
        },
        
        get selectedOptionsCount() {
            return this.selectedOptionsArray.length;
        },
        
        get filteredAvailableOptions() {
            return this.availableOptions;
        },
        
        updateSelectedOptionsArray() {
            // This is called when the selected options change
            this.$nextTick(() => {
                this.selectedSelected = [];
                this.updateAllSelectedSelected();
            });
        },
        
        async searchOptions() {
            this.loading = true;
            
            try {
                const response = await fetch(`/api/filament-multi-side-select/search`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        state_path: this.statePath,
                        search: this.search,
                        selected: this.state,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.availableOptions = Object.entries(data.options).map(([key, label]) => ({
                        key,
                        label
                    }));
                    
                    this.availableSelected = [];
                    this.updateAllAvailableSelected();
                }
            } catch (error) {
                console.error('Error searching options:', error);
            } finally {
                this.loading = false;
            }
        },
        
        moveToSelected() {
            if (this.availableSelected.length === 0) return;
            
            const selectedOptionsToMove = this.availableSelected.map(index => this.availableOptions[index]);
            
            // Update the state with newly selected options
            selectedOptionsToMove.forEach(option => {
                if (!this.state.includes(option.key)) {
                    this.state.push(option.key);
                    this.selectedOptions[option.key] = option.label;
                }
            });
            
            // Update the UI
            this.availableOptions = this.availableOptions.filter((_, index) => !this.availableSelected.includes(index));
            this.availableSelected = [];
            this.updateSelectedOptionsArray();
            
            // Trigger an Alpine change event so Livewire knows the state has changed
            this.$nextTick(() => {
                this.$dispatch('input', this.state);
            });
        },
        
        moveToAvailable() {
            if (this.selectedSelected.length === 0) return;
            
            // Remove selected items from state
            this.state = this.state.filter(key => !this.selectedSelected.includes(key));
            
            // Collect items to move to available
            const optionsToMoveBack = [];
            this.selectedSelected.forEach(key => {
                if (this.selectedOptions[key]) {
                    optionsToMoveBack.push({
                        key,
                        label: this.selectedOptions[key]
                    });
                    
                    // Remove from selected options
                    delete this.selectedOptions[key];
                }
            });
            
            // Add back to available options
            this.availableOptions = [...this.availableOptions, ...optionsToMoveBack];
            
            // Reset selections
            this.selectedSelected = [];
            this.updateSelectedOptionsArray();
            
            // Trigger an Alpine change event
            this.$nextTick(() => {
                this.$dispatch('input', this.state);
            });
        },
        
        selectAll() {
            this.availableSelected = Array.from({ length: this.availableOptions.length }, (_, i) => i);
            this.updateAllAvailableSelected();
        },
        
        deselectAll() {
            this.state = [];
            this.selectedOptions = {};
            this.selectedSelected = [];
            this.updateSelectedOptionsArray();
            
            // Trigger an Alpine change event
            this.$nextTick(() => {
                this.$dispatch('input', this.state);
            });
        },
        
        updateAllAvailableSelected() {
            this.allAvailableSelected = this.availableSelected.length === this.availableOptions.length && this.availableOptions.length > 0;
        },
        
        updateAllSelectedSelected() {
            this.allSelectedSelected = this.selectedSelected.length === this.selectedOptionsArray.length && this.selectedOptionsArray.length > 0;
        },
        
        toggleAllAvailable() {
            if (this.allAvailableSelected) {
                this.selectAll();
            } else {
                this.availableSelected = [];
            }
        },
        
        toggleAllSelected() {
            if (this.allSelectedSelected) {
                this.selectedSelected = this.selectedOptionsArray.map(option => option.key);
            } else {
                this.selectedSelected = [];
            }
        }
    };
}