# Database Setup Guide

## Prerequisites

- PHP 8.3+
- Laravel 12
- MySQL 8.0+ or PostgreSQL 14+
- Composer

## Quick Start

### 1. Environment Configuration

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pwa_ecommerce
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Run Migrations

```bash
# Run all migrations
php artisan migrate

# Run migrations with fresh database (WARNING: Drops all tables)
php artisan migrate:fresh

# Run migrations with seeding
php artisan migrate --seed
```

### 3. Verify Database

```bash
# Check migration status
php artisan migrate:status

# Rollback last migration batch
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset
```

## Database Schema

See [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) for complete schema documentation.

## Models & Relationships

### Core Models

1. **User** (`app/Models/User.php`)
   - Enhanced with vendor, cart, wishlist, and messaging relationships
   - Role-based: customer, vendor, admin

2. **Vendor** (`app/Models/Vendor.php`)
   - Business profile with GPS coordinates
   - Metrics: rating, sales, reviews, shipping performance

3. **Product** (`app/Models/Product.php`)
   - Full e-commerce features
   - Searchable via Laravel Scout
   - Multiple images, flash sales, collections

4. **Category** (`app/Models/Category.php`)
   - Hierarchical structure (parent-child)
   - Featured categories support

5. **Order** (`app/Models/Order.php`)
   - Complete order lifecycle
   - Status tracking: pending → processing → shipped → delivered
   - Payment integration ready

6. **Cart** & **CartItem** (`app/Models/Cart.php`, `app/Models/CartItem.php`)
   - Guest and authenticated carts
   - Auto-calculation of totals

7. **Message** & **Conversation** (`app/Models/Message.php`, `app/Models/Conversation.php`)
   - Real-time chat via Laravel Reverb
   - Unread counts, online status

8. **Review** (`app/Models/Review.php`)
   - Polymorphic: products and vendors
   - Verified purchase badge

## Repositories

### Available Repositories

All repositories follow the Repository Pattern with interfaces:

```php
// Vendor Repository
app/Repositories/Interfaces/VendorRepositoryInterface.php
app/Repositories/Eloquent/VendorRepository.php

// Product Repository
app/Repositories/Interfaces/ProductRepositoryInterface.php
app/Repositories/Eloquent/ProductRepository.php

// Category Repository
app/Repositories/Interfaces/CategoryRepositoryInterface.php
app/Repositories/Eloquent/CategoryRepository.php

// Order Repository
app/Repositories/Interfaces/OrderRepositoryInterface.php
app/Repositories/Eloquent/OrderRepository.php

// Cart Repository
app/Repositories/Interfaces/CartRepositoryInterface.php
app/Repositories/Eloquent/CartRepository.php
```

### Usage Example

```php
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->getActiveProducts(15);
        return view('products.index', compact('products'));
    }

    public function featured()
    {
        $featured = $this->productRepository->getFeaturedProducts(10);
        return view('products.featured', compact('featured'));
    }
}
```

## Seeders

### Create Seeders

```bash
# Category Seeder
php artisan make:seeder CategorySeeder

# Product Seeder
php artisan make:seeder ProductSeeder

# Vendor Seeder
php artisan make:seeder VendorSeeder
```

### Run Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=CategorySeeder
```

## Testing

### Create Factories

```bash
# Vendor Factory
php artisan make:factory VendorFactory

# Product Factory
php artisan make:factory ProductFactory
```

### Example Factory Usage

```php
// Create test data
$vendor = Vendor::factory()->create();
$products = Product::factory()->count(10)->create(['vendor_id' => $vendor->id]);
```

## Search Configuration

### Laravel Scout Setup

1. Install Scout:
```bash
composer require laravel/scout
```

2. Publish config:
```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

3. Install search driver (Meilisearch or Algolia):
```bash
# Meilisearch
composer require meilisearch/meilisearch-php

# Algolia
composer require algolia/algoliasearch-client-php
```

4. Import searchable models:
```bash
php artisan scout:import "App\Models\Product"
```

## Performance Optimization

### Database Indexes

All critical indexes are created via migrations:
- Foreign keys
- Unique constraints
- Composite indexes for common queries
- Full-text search on products

### Query Optimization

Use eager loading to prevent N+1 queries:

```php
// Good
$products = Product::with(['category', 'vendor', 'primaryImage'])->get();

// Bad
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // N+1 query
}
```

### Caching

Implement caching for frequently accessed data:

```php
$categories = Cache::remember('active_categories', 3600, function () {
    return Category::active()->get();
});
```

## Backup & Restore

### Backup Database

```bash
# MySQL
mysqldump -u username -p database_name > backup.sql

# PostgreSQL
pg_dump -U username database_name > backup.sql
```

### Restore Database

```bash
# MySQL
mysql -u username -p database_name < backup.sql

# PostgreSQL
psql -U username database_name < backup.sql
```

## Troubleshooting

### Common Issues

1. **Migration Failed**
   ```bash
   # Check database connection
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

2. **Foreign Key Constraint Errors**
   - Ensure migrations run in correct order
   - Check if referenced records exist

3. **Index Too Long Error (MySQL)**
   ```php
   // In AppServiceProvider::boot()
   Schema::defaultStringLength(191);
   ```

## Security Best Practices

1. **Always use parameterized queries** (Eloquent does this automatically)
2. **Validate all input data** using Form Requests
3. **Hash sensitive data** (passwords are auto-hashed)
4. **Use database transactions** for critical operations:

```php
DB::transaction(function () {
    // Your database operations
    Order::create([...]);
    Inventory::decrement('stock', 1);
});
```

## Maintenance

### Regular Tasks

```bash
# Optimize database tables
php artisan db:optimize

# Clear old cart sessions (add to scheduler)
Cart::where('expires_at', '<', now())->delete();

# Update search indexes
php artisan scout:import "App\Models\Product"
```

### Monitoring

Monitor these metrics:
- Slow query log
- Database connection pool
- Table sizes
- Index usage

## Additional Resources

- [Laravel Database Documentation](https://laravel.com/docs/database)
- [Eloquent ORM Documentation](https://laravel.com/docs/eloquent)
- [Laravel Scout Documentation](https://laravel.com/docs/scout)
- [Repository Pattern Guide](https://laravel.com/docs/repositories)

---

**Created**: October 14, 2025  
**Version**: 1.0

