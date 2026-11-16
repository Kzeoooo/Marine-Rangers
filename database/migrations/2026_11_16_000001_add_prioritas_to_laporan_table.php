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
        Schema::table('laporan', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan', 'prioritas')) {
                $table->string('prioritas', 20)->default('sedang')->after('status');
                $table->integer('skor_prioritas')->default(0)->after('prioritas');
                $table->index('prioritas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (Schema::hasColumn('laporan', 'prioritas')) {
                $table->dropIndex(['prioritas']);
                $table->dropColumn(['prioritas', 'skor_prioritas']);
            }
        });
    }
};
