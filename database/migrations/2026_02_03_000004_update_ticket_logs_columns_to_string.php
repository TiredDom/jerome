<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->dropColumn(['from_status', 'to_status']);
        });

        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->string('from_status')->nullable()->after('user_id');
            $table->string('to_status')->after('from_status');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->dropColumn(['from_status', 'to_status']);
        });

        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->enum('from_status', ['open', 'in_progress', 'resolved'])->nullable()->after('user_id');
            $table->enum('to_status', ['open', 'in_progress', 'resolved'])->after('from_status');
        });
    }
};
