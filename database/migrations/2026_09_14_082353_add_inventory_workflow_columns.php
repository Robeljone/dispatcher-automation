<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique('item_id');
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique(['item_id']);
        });
    }
};
