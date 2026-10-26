<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('restrict');
            $table->string('foto', 255);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('wilayah', 100);
            $table->text('deskripsi')->nullable();
            $table->dateTime('waktu_laporan');
            $table->string('status', 20)->default('baru');
            $table->timestamps();

            // Indexes untuk percepatan query peta dan filter dashboard
            $table->index(['latitude', 'longitude']);
            $table->index('status');
            $table->index('id_kategori');
            $table->index('wilayah');
            $table->index('waktu_laporan');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};

