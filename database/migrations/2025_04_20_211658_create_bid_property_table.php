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
        Schema::create('bid_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained('bids')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->text('value')->nullable(); // قيمة الخاصية لهذه المزايدة
            $table->timestamps();

            // منع تكرار نفس الخاصية لنفس المزايدة
            $table->unique(['bid_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_property');
    }
};
