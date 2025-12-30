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
       Schema::create('paystack_customers', function (Blueprint $table) {
            $table->id();
            $table->morphs('billable'); // billable_id and billable_type
            $table->string('paystack_id')->index(); // e.g., CUS_xxxx
            $table->string('email');
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paystack_customers');
    }
};