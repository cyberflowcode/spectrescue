<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores responder profile information (exorcists, ghost hunters, mediums).
     */
    public function up(): void
    {
        Schema::create('responders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('specialization'); // e.g. Exorcist, Ghost Hunter, Medium, Cleric
            $table->string('badge_number')->unique();
            $table->text('bio')->nullable();
            $table->string('verification_status')->default('pending'); // pending, approved, rejected
            $table->boolean('is_available')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responders');
    }
};
