<?php

namespace rlessi\FilamentMultiSideSelect\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MultiSideSelect extends Field
{
    protected string $view = 'filament-multi-side-select::components.multi-side-select';

    protected string|Closure|null $relationship = null;
    
    protected string|Closure|null $displayAttribute = null;
    
    protected string|Closure|null $titleAttribute = null;
    
    protected string|Closure|null $searchAttributes = [];
    
    protected Closure|null $searchQuery = null;
    
    protected int $itemsPerPage = 10;
    
    protected bool $searchable = true;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (MultiSideSelect $component, $state): array {
            if (! is_array($state)) {
                return [];
            }

            return array_map(fn ($item) => strval($item), $state);
        });
        
        $this->afterStateHydrated(static function (MultiSideSelect $component, $state): void {
            if ($state instanceof Collection) {
                $component->state($state->pluck($component->getRelationshipKeyName())->toArray());
                return;
            }
            
            if (! is_array($state)) {
                $component->state([]);
                return;
            }
            
            $component->state($state);
        });
    }
    
    public function relationship(string|Closure $relationship, string|Closure $displayAttribute, string|Closure|null $titleAttribute = null): static
    {
        $this->relationship = $relationship;
        $this->displayAttribute = $displayAttribute;
        $this->titleAttribute = $titleAttribute ?? $displayAttribute;
        
        return $this;
    }
    
    public function searchAttributes(array|string|Closure $attributes): static
    {
        $this->searchAttributes = $attributes;
        
        return $this;
    }
    
    public function searchQuery(Closure $callback): static
    {
        $this->searchQuery = $callback;
        
        return $this;
    }
    
    public function itemsPerPage(int $count): static
    {
        $this->itemsPerPage = $count;
        
        return $this;
    }
    
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;
        
        return $this;
    }
    
    public function getRelationship(): BelongsToMany|HasMany
    {
        return $this->evaluate($this->relationship);
    }
    
    public function getRelationshipKeyName(): string
    {
        return $this->getRelationship()->getRelated()->getKeyName();
    }
    
    public function getDisplayAttribute(): string
    {
        return $this->evaluate($this->displayAttribute);
    }
    
    public function getTitleAttribute(): string
    {
        return $this->evaluate($this->titleAttribute) ?? $this->getDisplayAttribute();
    }
    
    public function getSearchAttributes(): array
    {
        $attributes = $this->evaluate($this->searchAttributes);
        
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }
        
        if (empty($attributes)) {
            $attributes = [$this->getDisplayAttribute()];
        }
        
        return $attributes;
    }
    
    public function getSearchQuery(): ?Closure
    {
        return $this->searchQuery;
    }
    
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
    
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
    
    public function getOptions(): Collection
    {
        $relationship = $this->getRelationship();
        $relatedModel = $relationship->getRelated();
        $displayAttribute = $this->getDisplayAttribute();
        
        return $relatedModel->all()->mapWithKeys(function (Model $item) use ($displayAttribute) {
            return [$item->getKey() => $item->{$displayAttribute}];
        });
    }
    
    public function getSelectedOptions(): Collection
    {
        $state = $this->getState();
        
        if (empty($state)) {
            return collect();
        }
        
        $relationship = $this->getRelationship();
        $relatedModel = $relationship->getRelated();
        $displayAttribute = $this->getDisplayAttribute();
        
        return $relatedModel->whereIn($relatedModel->getKeyName(), $state)
            ->get()
            ->mapWithKeys(function (Model $item) use ($displayAttribute) {
                return [$item->getKey() => $item->{$displayAttribute}];
            });
    }
    
    public function getAvailableOptions(string $search = null): Collection
    {
        $state = $this->getState();
        $relationship = $this->getRelationship();
        $relatedModel = $relationship->getRelated();
        $displayAttribute = $this->getDisplayAttribute();
        
        $query = $relatedModel->query();
        
        if (! empty($state)) {
            $query->whereNotIn($relatedModel->getKeyName(), $state);
        }
        
        if ($search && $this->isSearchable()) {
            $searchQuery = $this->getSearchQuery();
            
            if ($searchQuery) {
                $query = $this->evaluate($searchQuery, [
                    'query' => $query,
                    'search' => $search,
                ]);
            } else {
                $searchAttributes = $this->getSearchAttributes();
                
                $query->where(function (Builder $query) use ($search, $searchAttributes) {
                    foreach ($searchAttributes as $attribute) {
                        $query->orWhere($attribute, 'like', "%{$search}%");
                    }
                });
            }
        }
        
        return $query->limit($this->getItemsPerPage())
            ->get()
            ->mapWithKeys(function (Model $item) use ($displayAttribute) {
                return [$item->getKey() => $item->{$displayAttribute}];
            });
    }
}