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
        Schema::table('guardians', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('children', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('keepers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('children', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('keepers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
