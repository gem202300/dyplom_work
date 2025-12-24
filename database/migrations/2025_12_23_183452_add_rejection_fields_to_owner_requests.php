<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owner_requests', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->boolean('can_resubmit')->default(true)->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('owner_requests', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'can_resubmit']);
        });
    }
};
