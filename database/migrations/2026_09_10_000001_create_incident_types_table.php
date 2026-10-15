<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores categories of paranormal incidents (e.g., Haunting, Demonic Possession).
     */
    public function up(): void
    {
        Schema::create('incident_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Poltergeist / Haunting, Possession
            $table->text('description')->nullable();
            $table->string('default_threat_level')->default('Medium'); // Low, Medium, High, Critical
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_types');
    }
};
