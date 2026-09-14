<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropForeign(['request_by']);
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreign('request_by')->references('id')->on('drivers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropForeign(['request_by']);
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreign('request_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
