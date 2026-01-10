<?php

use App\Models\Organization;
use App\Models\Term;
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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('description')->nullable();
            $table->string('location')->index();
            $table->string('location_map_link')->nullable();
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at')->index();
            $table->timestamp('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignIdFor(Organization::class)->constrained();
            $table->foreignIdFor(Term::class)->nullable()->constrained();
            $table->foreignIdFor(User::class, 'created_by')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
