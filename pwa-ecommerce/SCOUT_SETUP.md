# Laravel Scout Setup Guide

## ✅ Installation Complete

Laravel Scout has been successfully installed and configured for the PWA eCommerce platform.

## 📦 What's Installed

- **Package**: `laravel/scout` v10.20.0
- **Configuration**: Published to `config/scout.php`
- **Default Driver**: `collection` (in-memory, perfect for development)

## 🔧 Configuration

### Current Setup

The default driver is set to `collection`, which provides in-memory search without requiring external services like Algolia or Meilisearch.

```php
// config/scout.php
'driver' => env('SCOUT_DRIVER', 'collection'),
```

### Environment Configuration

Add to your `.env` file:

```env
# Scout Configuration
SCOUT_DRIVER=collection
SCOUT_QUEUE=false
```

### Production Options

For production, consider upgrading to:

1. **Meilisearch** (Recommended - Open Source)
   ```env
   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://localhost:7700
   MEILISEARCH_KEY=your-master-key
   ```

2. **Algolia** (Cloud-hosted)
   ```env
   SCOUT_DRIVER=algolia
   ALGOLIA_APP_ID=your-app-id
   ALGOLIA_SECRET=your-admin-key
   ```

3. **Database** (MySQL/PostgreSQL full-text search)
   ```env
   SCOUT_DRIVER=database
   ```

## 📝 Models Using Scout

Currently, Scout is enabled on:

### Product Model

```php
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'price' => $this->price,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'vendor_id' => $this->vendor_id,
        ];
    }
}
```

## 🚀 Usage Examples

### 1. Basic Search

```php
use App\Models\Product;

// Search products
$products = Product::search('laptop')->get();

// Search with pagination
$products = Product::search('laptop')->paginate(15);

// Search with filters
$products = Product::search('laptop')
    ->where('is_active', true)
    ->where('stock', '>', 0)
    ->get();
```

### 2. Advanced Queries

```php
// Search with relationships
$products = Product::search('laptop')
    ->query(fn ($builder) => $builder->with('vendor', 'category'))
    ->get();

// Search with custom ordering
$products = Product::search('laptop')
    ->orderBy('created_at', 'desc')
    ->get();

// Get specific fields only
$products = Product::search('laptop')
    ->select(['id', 'name', 'price'])
    ->get();
```

### 3. Indexing Commands

```bash
# Import all products to search index
php artisan scout:import "App\Models\Product"

# Flush search index
php artisan scout:flush "App\Models\Product"

# Delete and re-import
php artisan scout:flush "App\Models\Product"
php artisan scout:import "App\Models\Product"
```

### 4. Automatic Syncing

By default, models are automatically synced when:
- Creating a new record
- Updating an existing record
- Deleting a record

To disable auto-sync on a model:

```php
// In your Product model
public static function boot()
{
    parent::boot();

    // Disable automatic syncing
    static::disableSearchSyncing();
}
```

### 5. Conditional Indexing

Only index certain records:

```php
// In Product model
public function shouldBeSearchable()
{
    return $this->is_active && $this->stock > 0;
}
```

## 🎯 Integration with Filament

Scout works seamlessly with Filament resources. The search in ProductResource already uses Scout automatically.

### ProductResource Example

```php
// app/Filament/Resources/ProductResource.php
public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->searchable(); // This uses Scout automatically!
}
```

## 🔍 Frontend Search Implementation

### Web Routes

```php
// routes/web.php
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/api/search', [SearchController::class, 'api'])->name('search.api');
```

### Controller Example

```php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        $products = Product::search($query)
            ->where('is_active', true)
            ->paginate(20);
            
        return view('search.index', compact('products', 'query'));
    }
    
    public function api(Request $request)
    {
        $query = $request->input('q');
        
        $products = Product::search($query)
            ->where('is_active', true)
            ->take(10)
            ->get();
            
        return response()->json($products);
    }
}
```

### Blade View Example

```blade
{{-- resources/views/search/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Search Results for "{{ $query }}"</h1>
    
    @if($products->isEmpty())
        <p class="text-gray-600">No products found.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold">{{ $product->name }}</h3>
                    <p class="text-gray-600">{{ Str::limit($product->description, 100) }}</p>
                    <p class="text-lg font-bold text-primary-600">
                        ${{ number_format($product->price, 2) }}
                    </p>
                </div>
            @endforeach
        </div>
        
        {{ $products->links() }}
    @endif
</div>
@endsection
```

### Alpine.js Search Component

```blade
<div x-data="searchComponent()">
    <input 
        type="text" 
        x-model="query"
        @input.debounce.500ms="search()"
        placeholder="Search products..."
        class="w-full px-4 py-2 rounded-lg border"
    />
    
    <div x-show="results.length > 0" class="mt-2 bg-white shadow-lg rounded-lg">
        <template x-for="product in results" :key="product.id">
            <a :href="`/products/${product.slug}`" class="block p-4 hover:bg-gray-50">
                <h4 x-text="product.name" class="font-semibold"></h4>
                <p x-text="`$${product.price}`" class="text-primary-600"></p>
            </a>
        </template>
    </div>
</div>

<script>
function searchComponent() {
    return {
        query: '',
        results: [],
        
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            
            const response = await fetch(`/api/search?q=${this.query}`);
            this.results = await response.json();
        }
    }
}
</script>
```

## 📊 Performance Optimization

### 1. Queue Indexing

For better performance, queue the indexing operations:

```env
SCOUT_QUEUE=true
```

Then make sure your queue worker is running:

```bash
php artisan queue:work
```

### 2. Chunked Import

Import large datasets in chunks:

```bash
# Default chunk size is 500
php artisan scout:import "App\Models\Product"

# Custom chunk size
php artisan scout:import "App\Models\Product" --chunk=100
```

### 3. Soft Delete Handling

Configure whether to keep soft-deleted records in the index:

```php
// config/scout.php
'soft_delete' => false, // Set to true to keep soft-deleted records
```

## 🐛 Troubleshooting

### Issue: "Trait Searchable not found"

**Solution**: Clear config cache
```bash
php artisan config:clear
```

### Issue: Search not working

**Solution**: Re-import the models
```bash
php artisan scout:flush "App\Models\Product"
php artisan scout:import "App\Models\Product"
```

### Issue: Performance degradation with collection driver

**Solution**: Upgrade to Meilisearch or Algolia for production:

#### Install Meilisearch

```bash
# Using Docker
docker run -d -p 7700:7700 getmeili/meilisearch:latest

# Update .env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=

# Import data
php artisan scout:import "App\Models\Product"
```

## 📚 Additional Resources

- [Laravel Scout Documentation](https://laravel.com/docs/scout)
- [Meilisearch Documentation](https://www.meilisearch.com/docs)
- [Algolia Documentation](https://www.algolia.com/doc/)
- [Scout Extended](https://github.com/algolia/scout-extended) - Enhanced Algolia integration

## 🔐 Security Notes

1. **API Keys**: Never commit Algolia or Meilisearch keys to version control
2. **Rate Limiting**: Apply rate limiting to search endpoints
3. **Input Validation**: Always sanitize search queries

```php
// Example rate limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/search', [SearchController::class, 'index']);
});
```

## ✨ Next Steps

1. **Add more models to search** (Vendor, Category, etc.)
2. **Implement autocomplete** using Alpine.js
3. **Add search analytics** to track popular searches
4. **Upgrade to Meilisearch** for production
5. **Add search filters** (price range, category, etc.)

---

**Version**: Scout 10.20.0  
**Laravel**: 12.0  
**Last Updated**: October 14, 2025

✅ **Scout is now fully configured and ready to use!**

