# Filament v4 Quick Reference Card

## 📦 Standard Imports for Resources

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YourResource\Pages;
use App\Models\YourModel;

// Form field components (TextInput, Select, etc.)
use Filament\Forms\Components as Forms;

// Layout components (Section, Grid, Tabs, etc.)
use Filament\Schemas\Components as Layout;

// Schema structure (replaces Form in v3)
use Filament\Schemas\Schema;

// Resource base
use Filament\Resources\Resource;

// Table
use Filament\Tables\Table;

// Actions (ALL actions now in Filament\Actions, NOT Filament\Tables\Actions)
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;

// Utilities (Get/Set for dynamic form logic)
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
```

---

## 🏗️ Resource Template

```php
class YourResource extends Resource
{
    protected static ?string $model = YourModel::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Use Layout\ for structure
            Layout\Section::make('Information')
                ->schema([
                    // Use Forms\ for fields
                    Forms\TextInput::make('name')->required(),
                    Forms\Select::make('status')->options([...]),
                ]),
                
            Layout\Grid::make(2)
                ->schema([
                    Forms\DatePicker::make('date'),
                    Forms\Toggle::make('active'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([...])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYourResources::route('/'),
            'create' => Pages\CreateYourResource::route('/create'),
            'edit' => Pages\EditYourResource::route('/{record}/edit'),
        ];
    }
}
```

---

## 🎨 Component Cheat Sheet

### Form Fields (`Forms\*`)
```php
Forms\TextInput::make('name')
Forms\Select::make('category')
Forms\Textarea::make('description')
Forms\RichEditor::make('content')
Forms\Toggle::make('is_active')
Forms\Checkbox::make('accept_terms')
Forms\DatePicker::make('birth_date')
Forms\FileUpload::make('avatar')
Forms\ColorPicker::make('color')
Forms\Repeater::make('items')
```

### Layout Components (`Layout\*`)
```php
Layout\Section::make('Title')
Layout\Grid::make(2)
Layout\Tabs::make('Label')
Layout\Fieldset::make('Label')
Layout\Wizard::make()
Layout\Group::make()
Layout\Flex::make()
```

---

## 🔄 Migration from v3 to v4

| v3 | v4 |
|----|-----|
| `use Filament\Forms\Form;` | `use Filament\Schemas\Schema;` |
| `Form $form` | `Schema $schema` |
| `$form->schema([...])` | `$schema->schema([...])` |
| `Forms\Section::make()` | `Layout\Section::make()` |
| `Forms\Grid::make()` | `Layout\Grid::make()` |

---

## ⚡ Common Patterns

### Multi-Column Section
```php
Layout\Section::make('Details')
    ->schema([
        Forms\TextInput::make('name'),
        Forms\Select::make('type'),
        Forms\TextInput::make('code'),
    ])
    ->columns(3)
```

### Tabs
```php
Layout\Tabs::make('Content')
    ->tabs([
        Layout\Tabs\Tab::make('Basic')
            ->schema([
                Forms\TextInput::make('title'),
            ]),
        Layout\Tabs\Tab::make('Advanced')
            ->schema([
                Forms\Textarea::make('meta'),
            ]),
    ])
```

### Repeater
```php
Forms\Repeater::make('items')
    ->schema([
        Forms\TextInput::make('name'),
        Forms\TextInput::make('qty')->numeric(),
    ])
    ->columns(2)
```

---

## 🚫 Common Mistakes

### ❌ Wrong - Layout Components
```php
use Filament\Forms\Components\Section;  // Section is NOT here!
use Filament\Forms\Form;                // Deprecated in v4

Forms\Section::make()  // Section is in Layout, not Forms!
```

### ✅ Correct - Layout Components
```php
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;

Layout\Section::make()  // Correct!
Forms\TextInput::make() // Correct!
```

---

### ❌ Wrong - Actions
```php
use Filament\Tables\Actions\Action;      // Wrong namespace!
use Filament\Tables\Actions\EditAction;  // Wrong namespace!
```

### ✅ Correct - Actions
```php
use Filament\Actions\Action;      // Correct!
use Filament\Actions\EditAction;  // Correct!
```

---

### ❌ Wrong - Get/Set Utilities
```php
use Filament\Forms\Components\Get;  // Wrong namespace!

->options(function (Forms\Get $get) {  // Wrong!
    return Product::pluck('name', 'id');
})
```

### ✅ Correct - Get/Set Utilities
```php
use Filament\Schemas\Components\Utilities\Get;

->options(function (Get $get) {  // Correct!
    $type = $get('type');
    return Product::where('type', $type)->pluck('name', 'id');
})
```

---

**Last Updated:** October 14, 2025  
**Filament:** v4.1.8 | **Laravel:** 12.x | **PHP:** 8.3

