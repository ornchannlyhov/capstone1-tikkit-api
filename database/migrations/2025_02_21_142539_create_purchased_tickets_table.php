<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchased_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('offer_id')->nullable(); // New: Store applied offer ID
            $table->decimal('discount_amount', 10, 2)->default(0); // New: Store discount amount
            $table->string('qr_code')->unique();
            $table->enum('status', ['valid', 'used', 'invalid'])->default('valid');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('ticket_options')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('set null'); // New: Offer relation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchased_tickets');
    }
};
