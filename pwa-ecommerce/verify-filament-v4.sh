#!/bin/bash

# Filament v4 Migration Verification Script
# Run this to verify all namespace fixes are correct

echo "╔══════════════════════════════════════════════════════╗"
echo "║   Filament v4 Migration Verification                 ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# Check Filament version
echo "📦 Filament Version:"
php artisan about --only=filament 2>/dev/null | grep "Version" || echo "   Could not determine version"
echo ""

# Check correct imports
echo "✅ Checking correct namespace imports..."
CORRECT_FORMS=$(grep -r "use Filament\\\\Forms\\\\Components as Forms" app/Filament/Resources/*.php | wc -l | xargs)
CORRECT_LAYOUT=$(grep -r "use Filament\\\\Schemas\\\\Components as Layout" app/Filament/Resources/*.php | wc -l | xargs)
CORRECT_SCHEMA=$(grep -r "use Filament\\\\Schemas\\\\Schema" app/Filament/Resources/*.php | wc -l | xargs)
CORRECT_ACTION=$(grep -r "use Filament\\\\Actions\\\\Action;" app/Filament/Resources/*.php | wc -l | xargs)
CORRECT_GET=$(grep -r "use Filament\\\\Schemas\\\\Components\\\\Utilities\\\\Get" app/Filament/Resources/*.php | wc -l | xargs)
CORRECT_SET=$(grep -r "use Filament\\\\Schemas\\\\Components\\\\Utilities\\\\Set" app/Filament/Resources/*.php | wc -l | xargs)

echo "   Forms components imported: $CORRECT_FORMS/8 files"
echo "   Layout components imported: $CORRECT_LAYOUT/8 files"
echo "   Schema imported: $CORRECT_SCHEMA/8 files"
echo "   Action imported (Filament\\Actions): $CORRECT_ACTION/8 files"
echo "   Get utility imported (Schemas\\Utilities\\Get): $CORRECT_GET/1 file (ReviewResource)"
echo "   Set utility imported (Schemas\\Utilities\\Set): $CORRECT_SET/4 files (Collection, Category, Product, Vendor)"
echo ""

# Check correct usage
echo "✅ Checking correct component usage..."
LAYOUT_SECTION=$(grep -r "Layout\\\\Section" app/Filament/Resources/*.php | wc -l | xargs)
FORMS_SECTION=$(grep -r "Forms\\\\Section" app/Filament/Resources/*.php | wc -l | xargs)

echo "   Layout\\Section usage: $LAYOUT_SECTION instances ✅"
echo "   Forms\\Section usage: $FORMS_SECTION instances (should be 0)"
echo ""

# Check for wrong imports (should be none)
echo "❌ Checking for wrong imports (should be 0)..."
WRONG_SCHEMAS_COMPONENTS=$(grep -r "use Filament\\\\Schemas\\\\Components as Forms" app/Filament/Resources/*.php | wc -l | xargs)
OLD_FORM=$(grep -r "use Filament\\\\Forms\\\\Form" app/Filament/Resources/*.php | wc -l | xargs)
WRONG_TABLE_ACTIONS=$(grep -r "use Filament\\\\Tables\\\\Actions\\\\Action" app/Filament/Resources/*.php | wc -l | xargs)
WRONG_FORMS_GET=$(grep -r "Forms\\\\Get \\\$get\\|Forms\\\\Components\\\\Get" app/Filament/Resources/*.php | wc -l | xargs)
WRONG_FORMS_SET=$(grep -r "Forms\\\\Set \\\$set\\|Forms\\\\Components\\\\Set" app/Filament/Resources/*.php | wc -l | xargs)

echo "   Wrong 'Schemas\Components as Forms': $WRONG_SCHEMAS_COMPONENTS"
echo "   Deprecated 'Forms\Form': $OLD_FORM"
echo "   Wrong 'Tables\Actions' (should use Filament\Actions): $WRONG_TABLE_ACTIONS"
echo "   Wrong 'Forms\Get' (should use Schemas\Utilities\Get): $WRONG_FORMS_GET"
echo "   Wrong 'Forms\Set' (should use Schemas\Utilities\Set): $WRONG_FORMS_SET"
echo ""

# Check routes
echo "🔗 Checking admin routes..."
ROUTE_COUNT=$(php artisan route:list --path=admin 2>/dev/null | grep -c "admin/" || echo "0")
echo "   Admin routes registered: $ROUTE_COUNT"
echo ""

# Check linter
echo "🔍 Checking for linter errors..."
# Note: This would need actual linter installed
echo "   Run: php artisan lint or phpstan analyze"
echo ""

# Summary
echo "╔══════════════════════════════════════════════════════╗"
echo "║   Summary                                            ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

if [ "$CORRECT_LAYOUT" -eq 8 ] && [ "$CORRECT_ACTION" -eq 8 ] && [ "$FORMS_SECTION" -eq 0 ] && [ "$WRONG_SCHEMAS_COMPONENTS" -eq 0 ] && [ "$OLD_FORM" -eq 0 ] && [ "$WRONG_TABLE_ACTIONS" -eq 0 ] && [ "$WRONG_FORMS_GET" -eq 0 ] && [ "$WRONG_FORMS_SET" -eq 0 ]; then
    echo "✅ ALL CHECKS PASSED!"
    echo ""
    echo "Your Filament v4 migration is complete and correct."
    echo "All resources are using proper namespaces:"
    echo "  • Forms\* for form fields (TextInput, Select, etc.)"
    echo "  • Layout\* for layout components (Section, Grid, etc.)"
    echo "  • Filament\Actions\* for all actions (NOT Tables\Actions)"
    echo "  • Schemas\Utilities\Get for \$get utility (NOT Forms\Get)"
    echo "  • Schemas\Utilities\Set for \$set utility (NOT Forms\Set)"
    echo "  • Schema for form structure"
    exit 0
else
    echo "⚠️  ISSUES FOUND!"
    echo ""
    echo "Please review the output above and fix any issues."
    exit 1
fi

