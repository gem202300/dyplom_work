<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rating_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reason'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_reports');
    }
};
