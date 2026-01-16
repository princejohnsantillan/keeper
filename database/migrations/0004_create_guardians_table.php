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
        Schema::create('guardians', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('first_name', 80)->index();
            $table->string('middle_name', 80)->nullable()->index();
            $table->string('last_name', 80)->index();
            $table->date('birth_date')->index();
            $table->boolean('gender')->index();
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->foreignUlid('owner_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('guardian_id')->references('id')->on('guardians');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guardian_id']);
        });

        Schema::dropIfExists('guardians');
    }
};
