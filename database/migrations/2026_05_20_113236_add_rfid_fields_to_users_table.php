<?php
// database/migrations/xxxx_add_rfid_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nim untuk mahasiswa, nidn untuk dosen
            $table->string('nim_nidn', 20)->unique()->nullable()->after('email');
            
            // VARCHAR 64 — aman untuk HEX 8 char, 16 char, dan masa depan
            // Unique tapi nullable (belum semua mahasiswa punya kartu)
            $table->string('rfid_uid', 64)->unique()->nullable()->after('nim_nidn');
            
            // Role sistem
            $table->enum('role', ['mahasiswa', 'dosen', 'admin'])
                  ->default('mahasiswa')->after('rfid_uid');
            
            // Foto profil mahasiswa (untuk popup di monitor ruang)
            $table->string('foto')->nullable()->after('role');
            
            // Soft delete - data tidak pernah hilang permanen
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim_nidn', 'rfid_uid', 'role', 'foto', 'deleted_at']);
        });
    }
};