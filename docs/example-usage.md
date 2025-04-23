# Example Usage

Here are some examples of how to use the Filament Multi-Side Select component in different scenarios.

## Basic Example

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            MultiSideSelect::make('categories')
                ->relationship('categories', 'name')
                ->searchable(),
        ]);
}
```

## With Custom Search

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;
use Illuminate\Database\Eloquent\Builder;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            MultiSideSelect::make('tags')
                ->relationship('tags', 'name')
                ->searchable()
                ->searchQuery(function (Builder $query, string $search) {
                    return $query->where(function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('slug', 'like', "%{$search}%");
                    });
                }),
        ]);
}
```

## With BelongsToMany Relationship

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

// In a Post resource
public static function form(Form $form): Form
{
    return $form
        ->schema([
            MultiSideSelect::make('tags')
                ->relationship('tags', 'name')
                ->searchable()
                ->searchAttributes(['name', 'slug']),
        ]);
}
```

## With HasMany Relationship

```php
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

// In a User resource
public static function form(Form $form): Form
{
    return $form
        ->schema([
            MultiSideSelect::make('roles')
                ->relationship('roles', 'name')
                ->searchable(),
        ]);
}
```

## In a Resource Edit Page

```php
use App\Filament\Resources\ProductResource;
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                    
                Forms\Components\Textarea::make('description'),
                
                MultiSideSelect::make('categories')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->itemsPerPage(15),
            ]);
    }
}
```

## Styling with Tailwind Config

To customize the appearance further, you can modify your `tailwind.config.js` file:

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/your-vendor/filament-multi-side-select/resources/**/*.blade.php',
        // ... other content paths
    ],
    theme: {
        extend: {
            // ... your theme extensions
        },
    },
    plugins: [
        // ... your plugins
    ],
};
```

## Working with Livewire Components

If you're creating a custom Livewire component that uses the Multi-Side Select:

```php
use Filament\Forms;
use Livewire\Component;
use rlessi\FilamentMultiSideSelect\Components\MultiSideSelect;

class ManagePostCategories extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $postId;
    public $categories = [];
    
    public function mount(int $postId): void
    {
        $this->postId = $postId;
        $this->categories = Post::find($postId)->categories->pluck('id')->toArray();
    }
    
    protected function getFormSchema(): array
    {
        return [
            MultiSideSelect::make('categories')
                ->relationship('categories', 'name')
                ->searchable(),
        ];
    }
    
    public function save(): void
    {
        $post = Post::find($this->postId);
        $post->categories()->sync($this->categories);
        
        $this->notify('success', 'Categories updated successfully.');
    }
    
    public function render()
    {
        return view('livewire.manage-post-categories');
    }
}
```