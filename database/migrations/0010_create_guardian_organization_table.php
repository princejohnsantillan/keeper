<?php

use App\Models\Guardian;
use App\Models\Organization;
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
        Schema::create('guardian_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Guardian::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guardian_id', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_organization');
    }
};
