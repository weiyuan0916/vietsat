# Filament Admin Panel Setup

## Overview

This document describes the Filament 4.0 admin panel configuration, resources, widgets, and real-time notification system integrated with Laravel Reverb.

## 📦 What's Been Created

### 1. Filament Resources (8 Total)

All resources include:
- ✅ Search functionality
- ✅ Advanced filters
- ✅ Bulk actions
- ✅ Soft delete support (where applicable)
- ✅ Custom badges and status indicators
- ✅ Relationship management

#### VendorResource
**Location**: `app/Filament/Resources/VendorResource.php`
- Full vendor management with location mapping (latitude/longitude)
- Business metrics display (rating, total sales, reviews)
- Verification and trust badges
- Bulk activation/deactivation
- Shop logo and banner upload

#### ProductResource  
**Location**: `app/Filament/Resources/ProductResource.php`
- Complete product CRUD with inventory tracking
- Pricing management (regular price, sale price, cost)
- SEO fields (meta title, description, keywords)
- Product specifications (KeyValue field)
- Stock status management
- Featured/New product badges
- Bulk stock status updates

#### CategoryResource
**Location**: `app/Filament/Resources/CategoryResource.php`
- Hierarchical category structure (parent-child)
- Category icons and images
- Featured categories
- Drag-and-drop ordering

#### OrderResource
**Location**: `app/Filament/Resources/OrderResource.php`
- Complete order lifecycle management
- Payment and shipping tracking
- Order status workflow (pending → delivered)
- Customer and vendor assignment
- Bulk status updates
- Badge notifications for pending orders

#### ReviewResource
**Location**: `app/Filament/Resources/ReviewResource.php`
- Review approval system
- Polymorphic reviews (Products & Vendors)
- Star rating display
- Verified purchase badges
- Image attachments support
- Helpful count tracking

#### CollectionResource
**Location**: `app/Filament/Resources/CollectionResource.php`
- Product collections/bundles
- Featured collections
- Custom ordering

#### FlashSaleResource
**Location**: `app/Filament/Resources/FlashSaleResource.php`
- Time-based flash sales
- Quantity tracking (sold vs available)
- Active/ongoing/expired filters
- Automatic percentage calculation

#### UserResource
**Location**: `app/Filament/Resources/UserResource.php`
- User management
- Email verification tracking
- Bulk email verification

### 2. Reverb Events (4 Total)

**Location**: `app/Events/`

All events implement `ShouldBroadcast` for real-time notifications:

- **VendorCreated** - Broadcasts to `admin-notifications` channel
- **ProductCreated** - Broadcasts to `admin-notifications` and `products` channels
- **OrderCreated** - Broadcasts to admin, user, and vendor private channels
- **MessageSent** - Broadcasts to conversation private channel

### 3. Filament Notifications

**Location**: `app/Filament/Notifications/`

- `NewVendorNotification` - Notifies admins of new vendor registrations
- `NewProductNotification` - Notifies admins of new products
- `NewOrderNotification` - Notifies admins of new orders

**Event Listeners** (`app/Listeners/`):
- `SendVendorCreatedNotification`
- `SendProductCreatedNotification`
- `SendOrderCreatedNotification`

**Registered in**: `app/Providers/EventServiceProvider.php`

### 4. Dashboard Widgets (5 Total)

**Location**: `app/Filament/Widgets/`

#### StatsOverviewWidget
- Total Revenue with trend chart
- Total Orders with trend chart  
- Pending Orders count
- Active Products count
- Active Vendors count
- Total Users count

#### OrdersChart
- 30-day order trend line chart
- Uses Flowframe/Trend package

#### RevenueChart
- 30-day revenue bar chart
- Filtered by paid orders only

#### LatestOrdersWidget
- Table widget showing last 10 orders
- Quick view action
- Status and payment badges

#### LatestVendorsWidget
- Table widget showing last 10 vendors
- Quick view action
- Verification and status indicators

### 5. Admin Panel Provider

**Location**: `app/Providers/Filament/AdminPanelProvider.php`

**Features**:
- Custom branding ("PWA eCommerce")
- Amber primary color scheme
- Database notifications enabled
- 30-second notification polling
- Navigation groups:
  - Marketplace
  - Sales
  - Content
  - User Management
- Collapsible sidebar
- Full-width content layout

### 6. Database Migrations

**Location**: `database/migrations/2025_10_14_200000_create_notifications_table.php`
- Notifications table for database notifications

## ⚙️ Configuration

### Broadcasting (Reverb)

Ensure your `.env` has:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Filament

The AdminPanelProvider is automatically discovered by Filament. Access the admin panel at:

```
http://your-domain/admin
```

### Event Broadcasting

Make sure to run:

```bash
php artisan reverb:start
```

And in your frontend, subscribe to the channels:

```javascript
Echo.channel('admin-notifications')
    .listen('VendorCreated', (e) => {
        console.log('New vendor:', e);
    })
    .listen('ProductCreated', (e) => {
        console.log('New product:', e);
    })
    .listen('OrderCreated', (e) => {
        console.log('New order:', e);
    });
```

## ✅ Resolved Issues

### Filament 4.1 API Changes

All resources have been updated to comply with Filament 4.1 API changes:

1. **Form Schema**: Changed from `Form` to `Schema` class
   ```php
   // Updated signature
   public static function form(Schema $schema): Schema
   ```

2. **Property Type Declarations**: Added proper union types
   ```php
   protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
   protected static int|null $navigationSort = 1;
   ```

3. **Widget Properties**: Chart widgets now use non-static `$heading` property
   ```php
   protected ?string $heading = 'Orders Overview';
   ```

4. **Migration Order**: Fixed migration execution order to ensure proper foreign key relationships

## 📊 Features Summary

### Search & Filters
- All resources have searchable columns
- Advanced filter options (status, date ranges, relationships)
- TernaryFilters for boolean fields
- SelectFilters with multiple options

### Bulk Actions
- **Activate/Deactivate** - Most resources
- **Delete/Restore/Force Delete** - Soft-deletable resources
- **Status Updates** - Orders, Products
- **Approval Actions** - Reviews

### Soft Deletes
Enabled on:
- Vendors
- Products  
- Orders

### Navigation Badges
- VendorResource: Active vendor count
- ProductResource: Active product count  
- OrderResource: Pending order count (orange)
- FlashSaleResource: Active flash sale count (green)
- ReviewResource: Unapproved review count (orange)

### Relationships
Resources support viewing and managing:
- Vendor → Products, Orders
- Category → Products (with counts)
- Product → Vendor, Category
- Order → User, Vendor, Items
- Collection → Products

## 🔄 Workflow Example

1. **New Vendor Registers**:
   - `VendorCreated` event fires
   - Broadcasts to Reverb on `admin-notifications` channel
   - `SendVendorCreatedNotification` listener sends Filament notification
   - Admin sees notification in Filament panel
   - Admin can click to view vendor details

2. **New Product Added**:
   - `ProductCreated` event fires
   - Broadcasts to both `admin-notifications` and `products` channels
   - Filament notification sent to admins
   - Frontend users see real-time update on products page

3. **New Order Placed**:
   - `OrderCreated` event fires
   - Broadcasts to:
     - `admin-notifications` (admins)
     - `orders.{user_id}` (customer private channel)
     - `vendor.{vendor_id}` (vendor private channel)
   - All parties receive real-time notification
   - Order appears in LatestOrdersWidget on dashboard

## 🎨 Customization

### Colors
Edit in `AdminPanelProvider`:
```php
->colors([
    'primary' => Color::Amber, // Change to your brand color
])
```

### Navigation Groups
Add/modify in `AdminPanelProvider`:
```php
->navigationGroups([
    'Your Group Name',
])
```

Then in resources:
```php
public static function getNavigationGroup(): ?string
{
    return 'Your Group Name';
}
```

### Widgets Order
Set `$sort` property in widget classes:
```php
protected static ?int $sort = 1; // Lower number = higher priority
```

## 📝 Next Steps

1. **Run Migrations**:
```bash
php artisan migrate
```

2. **Create Admin User**:
```bash
php artisan make:filament-user
```

3. **Start Reverb**:
```bash
php artisan reverb:start
```

4. **Clear Cache**:
```bash
php artisan optimize:clear
```

5. **Access Admin Panel**:
```
http://localhost/admin
```

## 🔗 References

- [Filament Documentation](https://filamentphp.com/docs)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
- [Laravel Broadcasting](https://laravel.com/docs/broadcasting)

---

**Created**: October 14, 2025  
**Filament Version**: 4.0  
**Laravel Version**: 12.0

