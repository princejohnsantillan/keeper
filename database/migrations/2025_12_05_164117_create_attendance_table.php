<?php

use App\Models\Child;
use App\Models\Keeper;
use App\Models\Service;
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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class, 'service_id')->constrained();
            $table->foreignIdFor(Child::class, 'child_id')->constrained();

            $table->foreignIdFor(User::class, 'checkin_processed_by')->nullable()->constrained();
            $table->foreignIdFor(Keeper::class, 'checked_in_by')->nullable()->constrained();
            $table->timestamp('checked_in_at')->nullable();

            $table->foreignIdFor(User::class, 'checkout_processed_by')->nullable()->constrained();
            $table->foreignIdFor(Keeper::class, 'checked_out_by')->nullable()->constrained();
            $table->timestamp('checked_out_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps(); // created_at is when the child is registered for the service
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
