# Filament v4 - Get/Set Utility Namespace Fix

**Errors Fixed:**
1. `Argument #1 ($get) must be of type Filament\Forms\Components\Get, Filament\Schemas\Components\Utilities\Get given`
2. `Argument #1 ($set) must be of type Filament\Forms\Components\Set, Filament\Schemas\Components\Utilities\Set given`

**Root Cause:** Wrong namespace for Get/Set utility classes  
**Date Fixed:** October 14, 2025  
**Status:** ✅ **RESOLVED**

---

## 🔴 The Issue

### Error Message
```
App\Filament\Resources\ReviewResource::App\Filament\Resources\{closure}(): 
Argument #1 ($get) must be of type Filament\Forms\Components\Get, 
Filament\Schemas\Components\Utilities\Get given
```

### What Happened
In `ReviewResource.php`, there was a type hint using the old Filament v3 namespace:

```php:63:app/Filament/Resources/ReviewResource.php
->options(function (Forms\Get $get) {  // ❌ Wrong namespace!
    $type = $get('reviewable_type');
    // ...
})
```

The closure expects `Filament\Forms\Components\Get`, but Filament v4 passes `Filament\Schemas\Components\Utilities\Get`.

---

## 🔍 Root Cause

### Filament v3 vs v4 Namespace Change

| Component | Filament v3 | Filament v4 |
|-----------|-------------|-------------|
| **Get utility** | `Filament\Forms\Components\Get` | `Filament\Schemas\Components\Utilities\Get` |
| **Set utility** | `Filament\Forms\Components\Set` | `Filament\Schemas\Components\Utilities\Set` |

In Filament v4:
- Utilities have been **moved** to the `Schemas` namespace
- They're now under `Components\Utilities\` instead of directly in `Components`
- This aligns with the overall v4 architecture where schemas handle form structure

---

## ✅ The Solution

### Before (Incorrect)
```php
<?php

namespace App\Filament\Resources;

use Filament\Forms\Components as Forms;  // ← Get is NOT here in v4!
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;

class ReviewResource extends Resource
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Select::make('reviewable_id')
                ->options(function (Forms\Get $get) {  // ❌ Wrong!
                    $type = $get('reviewable_type');
                    return Product::where('type', $type)->pluck('name', 'id');
                })
        ]);
    }
}
```

### After (Correct)
```php
<?php

namespace App\Filament\Resources;

use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Components\Utilities\Get;  // ✅ Add this!
use Filament\Schemas\Schema;

class ReviewResource extends Resource
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Select::make('reviewable_id')
                ->options(function (Get $get) {  // ✅ Correct!
                    $type = $get('reviewable_type');
                    return Product::where('type', $type)->pluck('name', 'id');
                })
        ]);
    }
}
```

---

## 📝 What Was Changed

### Files Updated

#### For Get Utility:
- **`app/Filament/Resources/ReviewResource.php`**

#### For Set Utility:
- **`app/Filament/Resources/CollectionResource.php`**
- **`app/Filament/Resources/CategoryResource.php`**
- **`app/Filament/Resources/ProductResource.php`**
- **`app/Filament/Resources/VendorResource.php`**

### Changes Made

1. **Added Import (Get):**
```php
use Filament\Schemas\Components\Utilities\Get;
```

2. **Added Import (Set):**
```php
use Filament\Schemas\Components\Utilities\Set;
```

3. **Updated Type Hints:**
```php
// Before
->options(function (Forms\Get $get) {
->afterStateUpdated(fn (Forms\Set $set, ?string $state) => ...)

// After  
->options(function (Get $get) {
->afterStateUpdated(fn (Set $set, ?string $state) => ...)
```

---

## 🎯 When to Use Get/Set Utilities

### The Get Utility
Used to **read** values from other form fields in dynamic/reactive forms:

```php
use Filament\Schemas\Components\Utilities\Get;

Forms\Select::make('product_id')
    ->options(function (Get $get) {
        $categoryId = $get('category_id');  // Get value from 'category_id' field
        
        if (!$categoryId) {
            return [];
        }
        
        return Product::where('category_id', $categoryId)
            ->pluck('name', 'id');
    })
    ->searchable()
```

### The Set Utility
Used to **write** values to other form fields programmatically:

```php
use Filament\Schemas\Components\Utilities\Set;

Forms\Select::make('country')
    ->live()
    ->afterStateUpdated(function (Set $set, $state) {
        $set('currency', Country::find($state)?->currency);  // Auto-set currency
    })
```

### Using Both Together
```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

Forms\Select::make('product_id')
    ->options(function (Get $get) {
        $categoryId = $get('category_id');
        return Product::where('category_id', $categoryId)->pluck('name', 'id');
    })
    ->afterStateUpdated(function (Set $set, Get $get, $state) {
        $product = Product::find($state);
        $set('price', $product?->price);
        $set('stock', $product?->stock);
    })
```

---

## 🔬 Real-World Example (from ReviewResource)

This is the actual implementation in ReviewResource:

```php
use Filament\Schemas\Components\Utilities\Get;

Forms\Select::make('reviewable_type')
    ->label('Review Type')
    ->options([
        'App\\Models\\Product' => 'Product',
        'App\\Models\\Vendor' => 'Vendor',
    ])
    ->required()
    ->live(),  // ← Important: enables reactivity

Forms\Select::make('reviewable_id')
    ->label('Item')
    ->options(function (Get $get) {
        $type = $get('reviewable_type');  // Read the type selection
        
        if ($type === 'App\\Models\\Product') {
            return \App\Models\Product::pluck('name', 'id');
        } elseif ($type === 'App\\Models\\Vendor') {
            return \App\Models\Vendor::pluck('shop_name', 'id');
        }
        
        return [];
    })
    ->searchable()
    ->preload()
    ->required()
```

**How it works:**
1. User selects a "Review Type" (Product or Vendor)
2. The `reviewable_id` field uses `Get $get` to read that selection
3. Based on the type, it loads the appropriate options (Products or Vendors)
4. This creates a **dynamic, reactive form** where one field depends on another

---

## 🔬 Real-World Example 2 (Set Utility - Auto-generate Slug)

This is the actual implementation in CollectionResource, CategoryResource, ProductResource, and VendorResource:

```php
use Filament\Schemas\Components\Utilities\Set;

Forms\TextInput::make('name')
    ->required()
    ->maxLength(255)
    ->live(onBlur: true)  // ← Important: enables reactivity
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),

Forms\TextInput::make('slug')
    ->required()
    ->maxLength(255)
    ->unique(ignoreRecord: true),
```

**How it works:**
1. User types a name (e.g., "My Product Name")
2. When the field loses focus (`onBlur`), `afterStateUpdated` is triggered
3. The `Set $set` utility automatically generates a slug from the name
4. The slug field is populated with "my-product-name"
5. This creates an **auto-fill behavior** where one field updates another

---

## 📊 Complete Utility Import Reference

### For Dynamic Forms (Get)
```php
use Filament\Schemas\Components\Utilities\Get;

// Type hint in closures
->options(function (Get $get) { ... })
->visible(fn (Get $get) => $get('some_field') === 'value')
->disabled(fn (Get $get) => !$get('is_active'))
```

### For Programmatic Updates (Set)
```php
use Filament\Schemas\Components\Utilities\Set;

// Type hint in closures
->afterStateUpdated(function (Set $set, $state) { ... })
->afterStateHydrated(function (Set $set, $state) { ... })
```

### Both Together
```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

->afterStateUpdated(function (Set $set, Get $get, $state) {
    // Read from other fields with Get
    $category = $get('category');
    
    // Write to other fields with Set
    $set('calculated_value', $category * $state);
})
```

---

## 🚨 Critical Rules

### 1. **Always Import from Schemas\Utilities**
```php
✅ use Filament\Schemas\Components\Utilities\Get;
✅ use Filament\Schemas\Components\Utilities\Set;

❌ use Filament\Forms\Components\Get;  // Wrong! This doesn't exist in v4
❌ use Filament\Forms\Components\Set;  // Wrong! This doesn't exist in v4
```

### 2. **Type Hints Required**
When using `$get` or `$set` in closures with type hints, use the correct class:

```php
// ✅ Correct
->options(function (Get $get) { ... })
->afterStateUpdated(function (Set $set, Get $get) { ... })

// ❌ Wrong
->options(function (Forms\Get $get) { ... })  // Forms\Get doesn't exist!
```

### 3. **Live Mode Required for Reactivity**
For reactive forms using `Get`, the dependent field must have `->live()`:

```php
Forms\Select::make('category_id')
    ->live(),  // ← Required for reactivity!

Forms\Select::make('product_id')
    ->options(function (Get $get) {
        return Product::where('category_id', $get('category_id'))->get();
    })
```

---

## ✅ Verification

### Automated Check
The verification script now checks for this:

```bash
$ ./verify-filament-v4.sh

✅ Get utility imported (Schemas\Utilities\Get): 1/1 file (ReviewResource)
❌ Wrong 'Forms\Get' (should use Schemas\Utilities\Get): 0
```

### Manual Check
```bash
# ✅ Verify correct import
$ grep -r "use Filament\\Schemas\\Components\\Utilities\\Get" app/Filament/
app/Filament/Resources/ReviewResource.php:use Filament\Schemas\Components\Utilities\Get;

# ✅ Verify no wrong imports
$ grep -r "Forms\\Get" app/Filament/Resources/
# (no results = good!)
```

---

## 📚 Additional Utilities in v4

Besides `Get` and `Set`, other utilities also exist:

```php
// Component utilities
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

// State path utilities (for nested/complex forms)
use Filament\Forms\Components\Component;

// Validation utilities
use Filament\Forms\Components\Contracts\HasValidationRules;
```

---

## 🎓 Key Takeaway

**In Filament v4, all form utilities (Get, Set) live in the Schemas namespace, not Forms.**

This reflects the architectural shift in v4 where:
- **Forms\*** = Individual field components (TextInput, Select, etc.)
- **Schemas\*** = Form structure, layout, and utilities
- **Actions\*** = All actions (previously in Tables\Actions)

---

## 📋 Migration Checklist

When migrating Get/Set utilities to Filament v4:

- [ ] Replace `use Filament\Forms\Components\Get;` with `use Filament\Schemas\Components\Utilities\Get;`
- [ ] Replace `use Filament\Forms\Components\Set;` with `use Filament\Schemas\Components\Utilities\Set;`
- [ ] Update type hints: `Forms\Get $get` → `Get $get`
- [ ] Update type hints: `Forms\Set $set` → `Set $set`
- [ ] Ensure dependent fields have `->live()` for reactivity
- [ ] Test dynamic/reactive forms to ensure they work correctly

---

## ✅ Final Status

| Check | Result |
|-------|--------|
| Get utility import updated | ✅ ReviewResource (1/1) |
| Set utility import updated | ✅ Collection, Category, Product, Vendor (4/4) |
| No wrong Forms\Get imports | ✅ 0 found |
| No wrong Forms\Set imports | ✅ 0 found |
| Type hints corrected | ✅ All updated |
| Reactive forms working | ✅ Tested |
| Routes cached successfully | ✅ No errors |
| Verification script updated | ✅ Checks both Get & Set |

---

**Fix Status:** ✅ **100% COMPLETE**  
**All 5 resources updated. All Filament v4 utility namespace issues fully resolved!**

*Last updated: October 14, 2025*

