<?php

declare(strict_types=1);

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
        Schema::table('terms', function (Blueprint $table) {
            $table->renameColumn('deprecated_at', 'archived_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('deprecated_at', 'archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->renameColumn('archived_at', 'deprecated_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('archived_at', 'deprecated_at');
        });
    }
};
