<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('maintenance_garage')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
        Schema::create('maintenance_spare', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('maintenance_garage')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_detail');
        Schema::dropIfExists('maintenance_spare');
    }
};
