<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom peran, status akun, dan data verifikasi (README §6).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'petugas', 'pengguna'])->default('pengguna')->after('password');
            $table->enum('status', ['aktif', 'pending', 'ditolak'])->default('aktif')->after('role');
            $table->enum('user_type', ['mahasiswa', 'dosen', 'staf'])->nullable()->after('status');
            $table->string('identity_number', 30)->nullable()->after('user_type');
            $table->text('verification_note')->nullable()->after('identity_number');
            $table->timestamp('verified_at')->nullable()->after('verification_note');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'status', 'user_type', 'identity_number', 'verification_note', 'verified_at',
            ]);
        });
    }
};
