<?php

use App\Models\Child;
use App\Models\Keeper;
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
        Schema::create('relationship', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Keeper::class)->constrained();
            $table->foreignIdFor(Child::class)->constrained();
            $table->string('relationship_type')->index();
            $table->boolean('is_authorized_guardian')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship');
    }
};
