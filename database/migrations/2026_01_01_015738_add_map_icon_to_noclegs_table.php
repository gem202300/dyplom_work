<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noclegs', function (Blueprint $table) {
            $table->string('map_icon')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('noclegs', function (Blueprint $table) {
            $table->dropColumn('map_icon');
        });
    }
};