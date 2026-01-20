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
        Schema::table('vacation_requests', function (Blueprint $table) {
            $table->unsignedSmallInteger('requested_days')->default(0)->after('end_date');
            $table->unsignedSmallInteger('year')->nullable()->after('requested_days'); // opcional pero útil
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacation_requests', function (Blueprint $table) {
            //
        });
    }
};
