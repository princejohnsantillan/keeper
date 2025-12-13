<?php

use App\Models\Activity;
use App\Models\Child;
use App\Models\Guardian;
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
        Schema::create('gatepasses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Guardian::class)->constrained();
            $table->foreignIdFor(Child::class)->constrained();
            $table->foreignIdFor(Activity::class)->constrained();
            $table->string('code');
            $table->timestamps();

            $table->unique(['code', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepasses');
    }
};
