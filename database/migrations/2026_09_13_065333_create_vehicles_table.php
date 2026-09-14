<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type');
            $table->string('made_in');
            $table->string('model');
            $table->year('made_year');
            $table->string('chassis_no')->unique();
            $table->string('motor_no');
            $table->string('body_type');
            $table->string('color');
            $table->string('fuel_type');
            $table->unsignedInteger('motor_cc')->nullable();
            $table->decimal('weight_total', 12, 2)->nullable();
            $table->decimal('weight_single', 12, 2)->nullable();
            $table->decimal('load_amount', 12, 2)->nullable();
            $table->string('motor_capacity')->nullable();
            $table->unsignedInteger('total_cylinder')->nullable();
            $table->string('plate_no')->unique();
            $table->string('pre_plate_no')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
