<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['ruang_kelas', 'aula', 'laboratorium', 'alat', 'lapangan']);
            $table->string('location', 100);
            $table->unsignedInteger('capacity');
            $table->text('description')->nullable();
            $table->enum('status', ['aktif', 'dalam_perbaikan', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
