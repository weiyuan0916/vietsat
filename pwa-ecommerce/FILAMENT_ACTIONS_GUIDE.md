# Filament Actions - Quick Reference Guide

## 📌 Action Class Namespaces

### ✅ Correct Import for Table Actions

When adding actions to **Table widgets** or **Resource tables**, use:

```php
use Filament\Actions\Action;
```

**Example Usage in TableWidget:**
```php
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestVendorsWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(/* ... */)
            ->columns([/* ... */])
            ->actions([
                Action::make('view')
                    ->url(fn ($record) => /* ... */)
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
```

### ✅ Correct Import for Page Actions

When adding actions to **Filament Pages** (List, Edit, View pages), use:

```php
use Filament\Actions;
```

**Example Usage in Resource Page:**
```php
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

---

## 🚫 Common Mistakes

### ❌ WRONG - This namespace doesn't exist:
```php
use Filament\Tables\Actions\Action; // ❌ NO Action class here!
```

**Why it's wrong:**
- The `vendor/filament/tables/src/Actions/` directory only contains `HeaderActionsPosition.php`
- There is no `Action.php` class in this namespace

### ✅ CORRECT - Use this instead:
```php
use Filament\Actions\Action; // ✅ Correct!
```

---

## 📍 Action Class Locations

| Action Type | Correct Namespace | File Location |
|-------------|------------------|---------------|
| Table Actions | `Filament\Actions\Action` | `vendor/filament/actions/src/Action.php` |
| Page Actions | `Filament\Actions` | `vendor/filament/actions/src/` |
| Form Actions | `Filament\Actions\Action` | `vendor/filament/actions/src/Action.php` |
| Modal Actions | `Filament\Actions\Action` | `vendor/filament/actions/src/Action.php` |

---

## 🎯 Quick Decision Tree

**Need to add an action?**

1. **Is it in a Table?**
   - ✅ Use: `use Filament\Actions\Action;`
   - Then: `Action::make('name')`

2. **Is it in a Page header?**
   - ✅ Use: `use Filament\Actions;`
   - Then: `Actions\CreateAction::make()`

3. **Is it in a Form?**
   - ✅ Use: `use Filament\Actions\Action;`
   - Then: `Action::make('name')`

---

## 📚 Common Action Types

### Table Actions
```php
use Filament\Actions\Action;

Action::make('view')
    ->url(fn ($record) => route('view', $record))
    ->icon('heroicon-m-eye')
    ->openUrlInNewTab()
```

### Page Header Actions
```php
use Filament\Actions;

Actions\CreateAction::make()
Actions\EditAction::make()
Actions\DeleteAction::make()
Actions\ViewAction::make()
```

### Bulk Actions
```php
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

BulkActionGroup::make([
    DeleteBulkAction::make(),
])
```

### Custom Actions
```php
use Filament\Actions\Action;

Action::make('approve')
    ->requiresConfirmation()
    ->action(fn ($record) => $record->approve())
    ->color('success')
    ->icon('heroicon-m-check-circle')
```

---

## 🔗 Related Resources

- [Filament Actions Documentation](https://filamentphp.com/docs/4.x/actions/overview)
- [Table Actions](https://filamentphp.com/docs/4.x/tables/actions)
- [Page Actions](https://filamentphp.com/docs/4.x/panels/resources/editing-records#header-actions)

---

## ✨ Key Takeaway

> **Always use `Filament\Actions\Action` for table actions, NOT `Filament\Tables\Actions\Action`**

The `Filament\Tables\Actions` namespace exists but doesn't contain an `Action` class. All actions, whether for tables, pages, or forms, come from the `Filament\Actions` namespace.

---

**Last Updated**: October 14, 2025

