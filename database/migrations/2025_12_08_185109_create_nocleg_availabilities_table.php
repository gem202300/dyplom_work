<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nocleg_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nocleg_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('available_capacity')->nullable(); // null = default capacity
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->unique(['nocleg_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nocleg_availabilities');
    }
};
