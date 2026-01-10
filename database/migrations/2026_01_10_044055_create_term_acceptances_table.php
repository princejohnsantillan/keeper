<?php

use App\Models\Guardian;
use App\Models\Term;
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
        Schema::create('term_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Term::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Guardian::class)->constrained()->cascadeOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['term_id', 'guardian_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('term_acceptances');
    }
};
