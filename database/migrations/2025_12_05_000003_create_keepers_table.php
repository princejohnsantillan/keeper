<?php

use App\Models\Organization;
use App\Models\User;
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
        Schema::create('keepers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class, 'organization_id')->constrained();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keepers');
    }
};
