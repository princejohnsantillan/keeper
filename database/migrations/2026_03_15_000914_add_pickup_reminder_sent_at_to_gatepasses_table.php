<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gatepasses', function (Blueprint $table) {
            $table->timestamp('pickup_reminder_sent_at')->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('gatepasses', function (Blueprint $table) {
            $table->dropColumn('pickup_reminder_sent_at');
        });
    }
};
