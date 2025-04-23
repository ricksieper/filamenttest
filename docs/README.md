# Filament Multi-Side Select

A beautiful and functional multi-side select component for Filament v3 with Ajax search capabilities.

## Features

- Two-column list interface with items that can be moved between sides
- Ajax-powered search to dynamically filter available options
- Bulk selection capabilities for efficient item management
- Customizable display attributes and relationship handling
- Responsive design that adapts to different screen sizes
- Live preview of selected items
- Pagination support for large datasets
- Support for both belongs-to-many and has-many relationships

## Installation

You can install the package via composer:

```bash
composer require your-vendor/filament-multi-side-select
```

## Usage

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

MultiSideSelect::make('categories')
    ->relationship('categories', 'name')
    ->searchable()
    ->searchAttributes(['name', 'description'])
    ->itemsPerPage(15)
```

### Basic Usage

In your Filament form, use the component like this:

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Other fields...
            
            MultiSideSelect::make('categories')
                ->relationship('categories', 'name')
                ->searchable(),
                
            // Other fields...
        ]);
}
```

### Advanced Usage

For more advanced usage, you can customize the component further:

```php
MultiSideSelect::make('tags')
    ->relationship('tags', 'name', 'label')
    ->searchable()
    ->searchAttributes(['name', 'label', 'description'])
    ->searchQuery(function (Builder $query, string $search) {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%".strtolower($search)."%"]);
        });
    })
    ->itemsPerPage(20)
```

## API Reference

### Component Methods

| Method | Description |
|--------|-------------|
| `relationship(string $relationship, string $displayAttribute, ?string $titleAttribute = null)` | Set the relationship, display attribute, and optional title attribute. |
| `searchable(bool $searchable = true)` | Enable or disable search functionality. |
| `searchAttributes(array $attributes)` | Set the attributes to search against. |
| `searchQuery(Closure $callback)` | Customize the search query. |
| `itemsPerPage(int $count)` | Set the number of items per page for pagination. |

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.