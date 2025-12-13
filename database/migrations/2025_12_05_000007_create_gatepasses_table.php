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
            $table->string('code')->index();
            $table->timestamps();

            $table->unique(['activity_id', 'code']);
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
