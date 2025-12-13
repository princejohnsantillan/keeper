<?php

use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Activity::class,)->constrained();
            $table->foreignIdFor(Child::class)->constrained();

            $table->foreignIdFor(Keeper::class, 'checkin_keeper_id')->nullable()->constrained();
            $table->foreignIdFor(Gatepass::class, 'checkin_gatepass_id')->nullable()->constrained();
            $table->timestamp('checked_in_at')->nullable();

            $table->foreignIdFor(Keeper::class, 'checkout_keeper_id')->nullable()->constrained();
            $table->foreignIdFor(Gatepass::class, 'checkout_gatepass_id')->nullable()->constrained();
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
