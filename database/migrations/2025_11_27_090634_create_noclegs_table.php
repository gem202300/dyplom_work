<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noclegs', function (Blueprint $table) {
        $table->id();

        $table->string('title'); 
        $table->text('description')->nullable(); 
        $table->integer('capacity')->nullable(); 
        $table->string('object_type', 50); 

        $table->string('city');    
        $table->string('street');    
        $table->string('location')->nullable(); 

        $table->string('contact_phone')->nullable(); 
        $table->string('link')->nullable();

        $table->boolean('has_kitchen')->default(false);
        $table->boolean('has_parking')->default(false);
        $table->boolean('has_bathroom')->default(false);
        $table->boolean('has_wifi')->default(false);
        $table->boolean('has_tv')->default(false);
        $table->boolean('has_balcony')->default(false);

        $table->string('amenities_other')->nullable(); 

        $table->timestamps();
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('noclegs');
    }
};
