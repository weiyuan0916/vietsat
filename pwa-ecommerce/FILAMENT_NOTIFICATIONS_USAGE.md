# 📢 Filament Notifications Usage Guide

## Quick Reference for Notification System

This guide shows how to use the Filament notification system in your Laravel 12 PWA E-commerce platform.

---

## 🎯 Available Notifications

### 1. New Vendor Notification
```php
use App\Filament\Notifications\NewVendorNotification;
use App\Models\Vendor;

// When a new vendor registers
$vendor = Vendor::create([
    'user_id' => $user->id,
    'shop_name' => 'My Shop',
    // ... other fields
]);

// Send notification to all admins
NewVendorNotification::send($vendor);
```

**What it does:**
- ✅ Sends notification to all users with `role = 'admin'`
- ✅ Shows vendor shop name
- ✅ Includes "View Vendor" action button
- ✅ Links to Filament vendor detail page
- ✅ Success icon and color

---

### 2. New Product Notification
```php
use App\Filament\Notifications\NewProductNotification;
use App\Models\Product;

// When a new product is added
$product = Product::create([
    'vendor_id' => $vendor->id,
    'name' => 'Amazing Product',
    'price' => 99000,
    // ... other fields
]);

// Send notification to all admins
NewProductNotification::send($product);
```

**What it does:**
- ✅ Sends notification to all admins
- ✅ Shows product name and vendor name
- ✅ Includes "View Product" action button
- ✅ Links to Filament product detail page
- ✅ Info icon and color

---

### 3. New Order Notification
```php
use App\Filament\Notifications\NewOrderNotification;
use App\Models\Order;

// When a new order is placed
$order = Order::create([
    'user_id' => $user->id,
    'order_number' => 'ORD-2025-001',
    'total' => 150000,
    // ... other fields
]);

// Send notification to all admins
NewOrderNotification::send($order);
```

**What it does:**
- ✅ Sends notification to all admins
- ✅ Shows order number and total amount (formatted Vietnamese Dong)
- ✅ Includes "View Order" action button
- ✅ Links to Filament order detail page
- ✅ Warning icon and color

---

## 🔔 How Notifications Work

### Backend Flow

```
1. Event Occurs (New Vendor/Product/Order)
        ↓
2. Notification Class Called
        ↓
3. Query Admin Users: User::where('role', 'admin')->get()
        ↓
4. Send to Database: ->sendToDatabase($admins)
        ↓
5. Stored in 'notifications' table (Laravel default)
        ↓
6. Displayed in Filament Admin Panel
```

### Database Storage

Notifications are stored in Laravel's default `notifications` table:

```sql
-- Laravel's notifications table (created by Laravel)
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255),           -- Notification class name
    notifiable_type VARCHAR(255), -- App\Models\User
    notifiable_id BIGINT,        -- User ID
    data JSON,                   -- Notification content
    read_at TIMESTAMP NULL,      -- When user read it
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🎨 Notification Anatomy

Each notification includes:

```php
Notification::make()
    ->title('New Vendor Registered')           // Title text
    ->icon('heroicon-o-building-storefront')   // Heroicon
    ->iconColor('success')                     // success/info/warning/danger
    ->body("**Shop Name** has registered.")    // Body (supports Markdown)
    ->actions([                                 // Action buttons
        Action::make('view')
            ->label('View Vendor')
            ->url(route('filament.admin.resources.vendors.view', $vendor)),
    ])
    ->success()                                 // Badge color
    ->sendToDatabase($recipients);              // Send method
```

### Icon Options (Heroicons)

Common icons used:
- `heroicon-o-building-storefront` - Vendor/Shop
- `heroicon-o-shopping-bag` - Products
- `heroicon-o-shopping-cart` - Orders
- `heroicon-o-user-group` - Users
- `heroicon-o-bell` - Notifications
- `heroicon-o-check-circle` - Success
- `heroicon-o-exclamation-circle` - Warning

Full list: https://heroicons.com/

### Color Options

- `success` - Green (positive actions)
- `info` - Blue (informational)
- `warning` - Yellow/Orange (attention needed)
- `danger` - Red (critical/errors)

---

## 📊 Notification Types by Role

### Admin Users (`role = 'admin'`)

Receives:
- ✅ New Vendor registrations
- ✅ New Product additions
- ✅ New Order placements

### Vendor Users (`role = 'vendor'`)

Could receive (implement if needed):
- ✅ New orders for their products
- ✅ New product reviews
- ✅ Low stock alerts

### Customer Users (`role = 'customer'`)

Could receive (implement if needed):
- ✅ Order confirmation
- ✅ Order status updates
- ✅ Delivery notifications

---

## 💻 Using Notifications in Code

### Example: VendorObserver

```php
namespace App\Observers;

use App\Models\Vendor;
use App\Filament\Notifications\NewVendorNotification;

class VendorObserver
{
    public function created(Vendor $vendor): void
    {
        // Send notification when vendor is created
        NewVendorNotification::send($vendor);
    }
}
```

### Example: OrderController

```php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Filament\Notifications\NewOrderNotification;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = Order::create($request->validated());
        
        // Send notification to admins
        NewOrderNotification::send($order);
        
        return redirect()->route('orders.show', $order);
    }
}
```

### Example: Product Service

```php
namespace App\Services;

use App\Models\Product;
use App\Filament\Notifications\NewProductNotification;

class ProductService
{
    public function createProduct(array $data): Product
    {
        $product = Product::create($data);
        
        // Notify admins about new product
        NewProductNotification::send($product);
        
        return $product;
    }
}
```

---

## 📱 Viewing Notifications in Filament

### Admin Panel

Admins can view notifications in the Filament admin panel:

1. **Bell Icon** - Top right corner shows unread count
2. **Dropdown** - Click to see recent notifications
3. **Action Buttons** - Click "View..." to go to detail page
4. **Mark as Read** - Automatically marked when viewed

### Accessing Notifications

```php
// In any controller or page
$user = auth()->user();

// Get all notifications
$notifications = $user->notifications;

// Get unread notifications
$unread = $user->unreadNotifications;

// Mark as read
$user->notifications->markAsRead();

// Mark specific notification as read
$notification->markAsRead();
```

---

## 🧪 Testing Notifications

### 1. Create Test Admin User

```bash
php artisan tinker
```

```php
$admin = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);
```

### 2. Create Test Vendor and Trigger Notification

```php
$user = App\Models\User::create([
    'name' => 'Vendor User',
    'email' => 'vendor@test.com',
    'password' => bcrypt('password'),
    'role' => 'vendor',
]);

$vendor = App\Models\Vendor::create([
    'user_id' => $user->id,
    'shop_name' => 'Test Shop',
    'slug' => 'test-shop',
    'status' => 'active',
]);

// Send notification
App\Filament\Notifications\NewVendorNotification::send($vendor);
```

### 3. Check Notifications

```php
// Get admin user
$admin = App\Models\User::where('role', 'admin')->first();

// View notifications
$admin->notifications;
// or
$admin->unreadNotifications;
```

---

## 🔧 Customizing Notifications

### Add More Recipients

```php
// Send to admins and the vendor
$recipients = collect([
    ...User::where('role', 'admin')->get(),
    $vendor->user,
]);

Notification::make()
    ->title('...')
    // ... other properties
    ->sendToDatabase($recipients);
```

### Send to Specific Users

```php
// Send to specific user IDs
$recipients = User::whereIn('id', [1, 2, 3])->get();

Notification::make()
    ->title('...')
    ->sendToDatabase($recipients);
```

### Add More Actions

```php
->actions([
    Action::make('view')
        ->label('View Details')
        ->url(route('filament.admin.resources.vendors.view', $vendor)),
    
    Action::make('approve')
        ->label('Approve')
        ->action(fn () => $vendor->update(['status' => 'approved']))
        ->requiresConfirmation(),
    
    Action::make('reject')
        ->label('Reject')
        ->color('danger')
        ->action(fn () => $vendor->update(['status' => 'rejected'])),
])
```

---

## 📖 Related Documentation

- **Laravel Notifications:** https://laravel.com/docs/11.x/notifications
- **Filament Notifications:** https://filamentphp.com/docs/4.x/notifications/sending-notifications
- **Filament Actions:** https://filamentphp.com/docs/4.x/actions/overview
- **Heroicons:** https://heroicons.com/

---

## 🎉 Summary

**3 Ready-to-Use Notifications:**
- ✅ `NewVendorNotification::send($vendor)`
- ✅ `NewProductNotification::send($product)`
- ✅ `NewOrderNotification::send($order)`

**All notifications:**
- ✅ Send to admin users automatically
- ✅ Include action buttons for quick access
- ✅ Store in database for persistence
- ✅ Display in Filament admin panel
- ✅ Support unread badges and marking as read

**Ready to use! 🚀**

---

**Last Updated:** October 15, 2025  
**Laravel Version:** 12.x  
**Filament Version:** 4.1.8

