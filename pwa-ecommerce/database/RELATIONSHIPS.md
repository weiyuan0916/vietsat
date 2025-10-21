# Database Relationships Guide

## Overview

This document details all Eloquent relationships in the PWA E-commerce platform.

## Relationship Types

### 1. One-to-One
- User → Vendor
- User → Cart
- Product → FlashSale

### 2. One-to-Many
- Vendor → Products
- Vendor → Orders
- Category → Products
- Category → Categories (self-referencing)
- Product → ProductImages
- Product → Wishlists
- Product → CartItems
- Product → OrderItems
- Cart → CartItems
- Order → OrderItems
- Order → Reviews
- User → Orders
- User → Wishlists
- User → Reviews
- User → Conversations
- User → Messages (sent)
- User → Messages (received)
- User → Notifications
- Conversation → Messages

### 3. Many-to-Many
- Product ↔ Collection (via collection_product)

### 4. Polymorphic
- Review → Reviewable (Product or Vendor)

---

## Detailed Relationships

### User Model

```php
// One-to-One
public function vendor(): HasOne
public function cart(): HasOne

// One-to-Many
public function orders(): HasMany
public function wishlists(): HasMany
public function reviews(): HasMany
public function conversations(): HasMany
public function sentMessages(): HasMany
public function receivedMessages(): HasMany
public function notifications(): HasMany
```

**Usage:**
```php
$user = User::find(1);

// Get user's vendor profile
$vendor = $user->vendor;

// Get user's cart
$cart = $user->cart;

// Get user's orders
$orders = $user->orders()->latest()->get();

// Get user's wishlist products
$wishlistProducts = $user->wishlists()->with('product')->get();

// Get user's reviews
$reviews = $user->reviews()->approved()->get();
```

---

### Vendor Model

```php
// Belongs To
public function user(): BelongsTo

// One-to-Many
public function products(): HasMany
public function orders(): HasMany
public function conversations(): HasMany

// Polymorphic
public function reviews(): MorphMany
```

**Usage:**
```php
$vendor = Vendor::find(1);

// Get vendor's user account
$user = $vendor->user;

// Get vendor's products
$products = $vendor->products()->active()->get();

// Get vendor's orders
$orders = $vendor->orders()->pending()->get();

// Get vendor's reviews
$reviews = $vendor->reviews()->approved()->get();

// Get vendor's rating
$avgRating = $vendor->reviews()->avg('rating');
```

---

### Product Model

```php
// Belongs To
public function vendor(): BelongsTo
public function category(): BelongsTo

// One-to-Many
public function images(): HasMany
public function wishlists(): HasMany
public function cartItems(): HasMany
public function orderItems(): HasMany

// One-to-One
public function primaryImage(): HasOne
public function flashSale(): HasOne

// Many-to-Many
public function collections(): BelongsToMany

// Polymorphic
public function reviews(): MorphMany
```

**Usage:**
```php
$product = Product::find(1);

// Get product vendor
$vendor = $product->vendor;

// Get product category
$category = $product->category;

// Get all product images
$images = $product->images;

// Get primary image only
$primaryImage = $product->primaryImage;

// Get flash sale info
$flashSale = $product->flashSale;
if ($flashSale && $flashSale->isActive()) {
    echo "Flash price: {$flashSale->flash_price}";
}

// Get product collections
$collections = $product->collections;

// Get product reviews
$reviews = $product->reviews()->approved()->get();

// Get wishlist count
$wishlistCount = $product->wishlists()->count();
```

---

### Category Model

```php
// Self-Referencing
public function parent(): BelongsTo
public function children(): HasMany

// One-to-Many
public function products(): HasMany
```

**Usage:**
```php
$category = Category::find(1);

// Get parent category
$parent = $category->parent;

// Get child categories
$children = $category->children;

// Get category products
$products = $category->products()->active()->get();

// Get category tree
$rootCategories = Category::parent()->with('children')->get();

// Get products with category
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name;
}
```

---

### Order Model

```php
// Belongs To
public function user(): BelongsTo
public function vendor(): BelongsTo

// One-to-Many
public function items(): HasMany
public function reviews(): HasMany
```

**Usage:**
```php
$order = Order::find(1);

// Get order customer
$customer = $order->user;

// Get order vendor
$vendor = $order->vendor;

// Get order items with products
$items = $order->items()->with('product')->get();

// Calculate order total
$total = $order->items->sum('subtotal');

// Get order reviews
$reviews = $order->reviews;

// Update order status
$order->update(['status' => 'shipped', 'shipped_at' => now()]);
```

---

### Cart Model

```php
// Belongs To
public function user(): BelongsTo

// One-to-Many
public function items(): HasMany
```

**Usage:**
```php
$cart = Cart::find(1);

// Get cart owner
$user = $cart->user;

// Get cart items with products
$items = $cart->items()->with('product.primaryImage')->get();

// Calculate cart total
$cart->calculateTotals();

// Add item to cart
$cartItem = $cart->items()->create([
    'product_id' => $productId,
    'quantity' => 1,
    'price' => $product->effective_price,
    'subtotal' => $product->effective_price,
]);

// Get cart items count
$itemsCount = $cart->items->sum('quantity');
```

---

### Conversation Model

```php
// Belongs To
public function user(): BelongsTo
public function vendor(): BelongsTo

// One-to-Many
public function messages(): HasMany

// One-to-One (Latest)
public function latestMessage(): HasOne
```

**Usage:**
```php
$conversation = Conversation::find(1);

// Get conversation participants
$user = $conversation->user;
$vendor = $conversation->vendor;

// Get all messages
$messages = $conversation->messages()->latest()->get();

// Get latest message
$latestMessage = $conversation->latestMessage;

// Create new message
$message = $conversation->messages()->create([
    'sender_id' => $userId,
    'receiver_id' => $vendorId,
    'message' => 'Hello!',
]);

// Mark all messages as read
$conversation->messages()
    ->where('receiver_id', $userId)
    ->unread()
    ->update(['is_read' => true, 'read_at' => now()]);
```

---

### Review Model (Polymorphic)

```php
// Belongs To
public function user(): BelongsTo
public function order(): BelongsTo

// Polymorphic
public function reviewable(): MorphTo
```

**Usage:**
```php
// Create product review
$review = Review::create([
    'user_id' => $userId,
    'reviewable_id' => $productId,
    'reviewable_type' => Product::class,
    'rating' => 5,
    'comment' => 'Great product!',
]);

// Create vendor review
$review = Review::create([
    'user_id' => $userId,
    'reviewable_id' => $vendorId,
    'reviewable_type' => Vendor::class,
    'rating' => 4,
    'comment' => 'Good service',
]);

// Get reviewable item
$reviewable = $review->reviewable; // Product or Vendor

// Query product reviews
$productReviews = Review::where('reviewable_type', Product::class)
    ->where('reviewable_id', $productId)
    ->approved()
    ->get();

// Query vendor reviews
$vendorReviews = Review::where('reviewable_type', Vendor::class)
    ->where('reviewable_id', $vendorId)
    ->approved()
    ->get();
```

---

## Query Optimization Tips

### Eager Loading

**Problem: N+1 Queries**
```php
// BAD - N+1 Query Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->vendor->shop_name; // Executes query for each product
    echo $product->category->name;    // Executes query for each product
}
```

**Solution: Eager Loading**
```php
// GOOD - Eager Loading
$products = Product::with(['vendor', 'category', 'primaryImage'])->get();
foreach ($products as $product) {
    echo $product->vendor->shop_name; // No additional query
    echo $product->category->name;    // No additional query
}
```

### Lazy Eager Loading

```php
$products = Product::all();

// Load relationships after initial query
$products->load(['vendor', 'category']);
```

### Nested Eager Loading

```php
// Load products with category and category's parent
$products = Product::with('category.parent')->get();

// Load orders with items and item's product images
$orders = Order::with('items.product.images')->get();
```

### Constraining Eager Loads

```php
// Load only active products for vendor
$vendor = Vendor::with(['products' => function ($query) {
    $query->active()->latest();
}])->find(1);

// Load only approved reviews
$product = Product::with(['reviews' => function ($query) {
    $query->approved()->latest();
}])->find(1);
```

### Counting Related Models

```php
// Get vendors with products count
$vendors = Vendor::withCount('products')->get();
foreach ($vendors as $vendor) {
    echo $vendor->products_count; // No additional query
}

// Get products with reviews count and average rating
$products = Product::withCount('reviews')
    ->withAvg('reviews', 'rating')
    ->get();
```

### Existence Queries

```php
// Get users who have orders
$users = User::has('orders')->get();

// Get vendors with at least 10 products
$vendors = Vendor::has('products', '>=', 10)->get();

// Get products with approved reviews
$products = Product::whereHas('reviews', function ($query) {
    $query->approved();
})->get();
```

---

## Advanced Relationship Queries

### Many-to-Many with Pivot Data

```php
// Attach product to collection with order
$collection->products()->attach($productId, ['order' => 1]);

// Get products with pivot data
$products = $collection->products()->get();
foreach ($products as $product) {
    echo $product->pivot->order;
}

// Update pivot data
$collection->products()->updateExistingPivot($productId, ['order' => 2]);

// Detach product from collection
$collection->products()->detach($productId);

// Sync products (attach new, detach removed)
$collection->products()->sync([1, 2, 3]);
```

### Polymorphic Relationships

```php
// Get all reviews for a product
$product = Product::find(1);
$reviews = $product->reviews;

// Get all reviews for a vendor
$vendor = Vendor::find(1);
$reviews = $vendor->reviews;

// Create review for product
$product->reviews()->create([
    'user_id' => $userId,
    'rating' => 5,
    'comment' => 'Excellent!',
]);

// Create review for vendor
$vendor->reviews()->create([
    'user_id' => $userId,
    'rating' => 4,
    'comment' => 'Good service',
]);
```

---

## Best Practices

### 1. Always Use Relationships
```php
// Good
$product->vendor->shop_name;

// Bad
$vendor = Vendor::find($product->vendor_id);
$vendor->shop_name;
```

### 2. Use Eager Loading for Collections
```php
// Good
$products = Product::with('vendor')->get();

// Bad (N+1 problem)
$products = Product::all();
```

### 3. Use Query Scopes
```php
// Good
$products = Product::active()->featured()->get();

// Bad
$products = Product::where('is_active', true)
    ->where('is_featured', true)
    ->get();
```

### 4. Use Transactions for Related Operations
```php
DB::transaction(function () use ($order, $cartItems) {
    // Create order
    $order->save();
    
    // Create order items
    foreach ($cartItems as $item) {
        $order->items()->create([...]);
        
        // Update product stock
        $item->product->decrement('stock_quantity', $item->quantity);
    }
    
    // Clear cart
    Cart::where('user_id', $userId)->delete();
});
```

---

**Last Updated**: October 14, 2025  
**Version**: 1.0

