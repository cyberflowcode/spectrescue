<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores paranormal emergency incidents reported by citizens.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('incident_type_id')->constrained('incident_types')->onDelete('restrict');
            $table->string('tracking_code')->unique(); // e.g. PAR-2026-001
            $table->string('title');
            $table->text('description');
            $table->string('location_address');
            $table->string('location_city');
            $table->string('threat_level')->default('Medium'); // Low, Medium, High, Critical
            $table->string('status')->default('Pending'); // Pending, Dispatched, Responding, Resolved, Cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
