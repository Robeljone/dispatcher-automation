<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_detail', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1);
        });

        Schema::table('maintenance_spare', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1);
        });

        Schema::table('onroad_detail', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1);
        });

        Schema::table('maintenance_own', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_detail', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('maintenance_spare', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('onroad_detail', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('maintenance_own', function (Blueprint $table) {
            $table->dropColumn('total');
        });
    }
};
