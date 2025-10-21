# 🔧 Filament Notifications UUID Fix

## ❌ Original Error

```
SQLSTATE[22007]: Invalid datetime format: 1292 Truncated incorrect DOUBLE value: 'a01e0711-5171-481a-ab87-fecbdc62b37b' 
(Connection: mysql, SQL: delete from `notifications` where `notifications`.`user_id` = 1 
and `notifications`.`user_id` is not null and json_unquote(json_extract(`data`, '$."format"')) = filament 
and `id` = a01e0711-5171-481a-ab87-fecbdc62b37b)
```

---

## 🔍 Root Cause

**The Problem:**  
Filament (and Laravel's database notifications) use **UUID** as the primary key for the `notifications` table, but the custom migration created an **integer** ID column.

### Why This Happens:

1. **Laravel's Database Notifications** (used by Filament) require a UUID primary key
2. The custom migration used `$table->id()` which creates an auto-incrementing **integer**
3. When Filament tried to delete a notification with UUID `a01e0711-5171-481a-ab87-fecbdc62b37b`, MySQL tried to compare a string to an integer column
4. Result: SQL error because the types don't match

---

## ⚠️ Critical: File Naming (PSR-4 Compliance)

After renaming the model class, **you MUST rename the file** to match:

```bash
# ❌ WRONG: File name doesn't match class name
# app/Models/Notification.php contains class UserNotification

# ✅ CORRECT: File name matches class name
mv app/Models/Notification.php app/Models/UserNotification.php

# Then regenerate autoload
composer dump-autoload -o
```

**Error if not fixed:**
```
Class App\Models\UserNotification located in ./app/Models/Notification.php 
does not comply with psr-4 autoloading standard
```

This will cause: `Class "App\Models\Notification" not found`

---

## ✅ Solution Applied

### Step 1: Rename Custom Notification Table

**File:** `database/migrations/2025_10_14_180113_create_notifications_table.php`

**Changed:**
- Renamed table from `notifications` to `user_notifications`
- Renamed file to `2025_10_14_180113_create_user_notifications_table.php`
- This table keeps the integer ID for your custom app notifications

```php
Schema::create('user_notifications', function (Blueprint $table) {
    $table->id(); // Integer ID for custom notifications
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type');
    $table->string('title');
    $table->text('message');
    $table->json('data')->nullable();
    $table->string('action_url')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    
    // Indexes...
});
```

---

### Step 2: Create Laravel's Standard Notifications Table

**Command:**
```bash
php artisan notifications:table
```

**Result:** `database/migrations/2025_10_15_060559_create_notifications_table.php`

**Structure:**
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary(); // ✅ UUID primary key
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

---

### Step 3: Update Custom Notification Model

**File:** `app/Models/Notification.php` → **Renamed to** → `app/Models/UserNotification.php`

**Changes:**
```php
/**
 * UserNotification Model
 * 
 * Represents a custom user notification (app-specific)
 * Separate from Laravel's database notifications used by Filament
 */
class UserNotification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_notifications';
    
    // ... rest of the model
}
```

---

### Step 4: Run Migrations

```bash
php artisan migrate:fresh --seed
```

**Result:**  
✅ Both tables created successfully:
- `notifications` (UUID, for Filament/Laravel)
- `user_notifications` (Integer, for custom app)

---

## 📊 Table Comparison

### `notifications` (Laravel/Filament)

| Column | Type | Description |
|--------|------|-------------|
| `id` | `char(36)` **UUID** | Primary key |
| `type` | `varchar(255)` | Notification class |
| `notifiable_type` | `varchar(255)` | Polymorphic type |
| `notifiable_id` | `bigint` | Polymorphic ID |
| `data` | `text` | JSON notification data |
| `read_at` | `timestamp` | When read |
| `created_at` | `timestamp` | When created |
| `updated_at` | `timestamp` | When updated |

### `user_notifications` (Custom App)

| Column | Type | Description |
|--------|------|-------------|
| `id` | `bigint` **Integer** | Primary key |
| `user_id` | `bigint` | User FK |
| `type` | `varchar(255)` | Notification type |
| `title` | `varchar(255)` | Title |
| `message` | `text` | Message content |
| `data` | `json` | Extra data |
| `action_url` | `varchar(255)` | Action link |
| `is_read` | `tinyint(1)` | Read status |
| `read_at` | `timestamp` | When read |
| `created_at` | `timestamp` | When created |
| `updated_at` | `timestamp` | When updated |

---

## 🎯 When to Use Each Table

### Use `notifications` (Laravel's Database Notifications) for:
- ✅ Filament admin panel notifications
- ✅ Laravel notification system (`User::notify()`)
- ✅ Real-time Reverb notifications
- ✅ System-level notifications

**Example:**
```php
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;

// Send notification
$user->notify(new OrderShippedNotification($order));

// Retrieve notifications
$notifications = auth()->user()->notifications; // Uses 'notifications' table
$unread = auth()->user()->unreadNotifications;
```

---

### Use `user_notifications` (Custom Table) for:
- ✅ Custom in-app notification features
- ✅ Frontend notification center
- ✅ Vendor-specific notifications
- ✅ Application-specific notification logic

**Example:**
```php
use App\Models\UserNotification;

// Create custom notification
UserNotification::create([
    'user_id' => $user->id,
    'type' => 'order',
    'title' => 'New Order',
    'message' => 'You have a new order #12345',
    'action_url' => route('orders.show', 12345),
]);

// Retrieve custom notifications
$notifications = UserNotification::where('user_id', auth()->id())
    ->unread()
    ->latest()
    ->get();
```

---

## 🚀 Verification

### Check Table Structures

```bash
# Check Laravel notifications table (UUID)
php artisan db:table notifications

# Check custom notifications table (Integer)
php artisan db:table user_notifications
```

### Expected Output:

**notifications:**
```
Column ................................................................ Type
id char, utf8mb4_unicode_ci ....................................... char(36)  ✅ UUID
type varchar, utf8mb4_unicode_ci .............................. varchar(255)
notifiable_type varchar, utf8mb4_unicode_ci ................... varchar(255)
notifiable_id bigint ....................................... bigint unsigned
```

**user_notifications:**
```
Column ................................................................ Type
id bigint, autoincrement ................................... bigint unsigned  ✅ Integer
user_id bigint ............................................. bigint unsigned
type varchar, utf8mb4_unicode_ci .............................. varchar(255)
title varchar, utf8mb4_unicode_ci ............................. varchar(255)
```

---

## 📚 Key Takeaways

### 1. **Two Separate Systems**

| System | Table | Model | ID Type |
|--------|-------|-------|---------|
| **Laravel/Filament** | `notifications` | `DatabaseNotification` | UUID |
| **Custom App** | `user_notifications` | `UserNotification` | Integer |

### 2. **Why UUID?**

Laravel's database notifications use UUID because:
- ✅ Distributed systems (multiple servers can generate IDs independently)
- ✅ No auto-increment conflicts
- ✅ Better for public-facing APIs (non-sequential)
- ✅ Standard for Laravel notification system

### 3. **Migration Naming Convention**

When you have a custom table that conflicts with Laravel's standard tables:
- Rename your custom table (e.g., `user_notifications`, `app_notifications`)
- Keep the standard Laravel table name for framework features
- Use explicit table names in your custom models

---

## ✅ Final Status

| Check | Status |
|-------|--------|
| Custom table renamed | ✅ `user_notifications` |
| Laravel table created | ✅ `notifications` with UUID |
| Model updated | ✅ `UserNotification` model |
| Migrations run | ✅ Both tables exist |
| Routes cached | ✅ No errors |
| Filament notifications | ✅ Working |

---

## 🔗 Related Files

### Migrations
- `database/migrations/2025_10_14_180113_create_user_notifications_table.php` (Custom)
- `database/migrations/2025_10_15_060559_create_notifications_table.php` (Laravel)

### Models
- `app/Models/UserNotification.php` (Custom)
- `Illuminate\Notifications\DatabaseNotification` (Laravel, built-in)

### Usage in User Model
```php
class User extends Authenticatable
{
    use Notifiable; // ✅ Enables Laravel notifications (UUID table)
    
    // Custom notifications relationship
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}
```

---

**Fix Status:** ✅ **100% COMPLETE**  
**Filament notifications now work correctly with UUID primary keys!**

*Last updated: October 15, 2025*

