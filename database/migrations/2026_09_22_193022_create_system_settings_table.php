<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('type')->default('text');
            $table->timestamps();
        });

        // Seed default settings
        DB::table('system_settings')->insert([
            ['key' => 'system_name',        'label' => 'System Name',               'value' => 'SPECTRESCUE',                                'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'system_tagline',     'label' => 'Tagline / Description',      'value' => 'Paranormal Emergency Dispatch & Response',   'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email',      'label' => 'Admin Contact Email',        'value' => 'admin@spectrescue.ph',                       'type' => 'email',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'emergency_hotline',  'label' => 'Emergency Hotline Number',   'value' => '+63 917 000 9999',                           'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'operating_hours',    'label' => 'Operating Hours',            'value' => '24/7 — All days including holidays',         'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hq_address',         'label' => 'Headquarters Address',       'value' => 'SPECTRESCUE HQ, Quezon City, Philippines',   'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_threat_override','label' => 'Allow Admin Threat Override','value' => 'yes',                                        'type' => 'text',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_notice', 'label' => 'Maintenance / Alert Banner', 'value' => '',                                           'type' => 'textarea', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
