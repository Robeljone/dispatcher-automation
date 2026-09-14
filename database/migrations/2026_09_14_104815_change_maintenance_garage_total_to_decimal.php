<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_garage', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_garage', function (Blueprint $table) {
            $table->integer('total')->default(0)->change();
        });
    }
};
