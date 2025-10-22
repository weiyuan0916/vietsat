<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('update_file_path')->nullable()->after('force_update');
            $table->string('update_file_version')->nullable()->after('update_file_path');
            $table->bigInteger('update_file_size')->nullable()->after('update_file_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['update_file_path', 'update_file_version', 'update_file_size']);
        });
    }
};
