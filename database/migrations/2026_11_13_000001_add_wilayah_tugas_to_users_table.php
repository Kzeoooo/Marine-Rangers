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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'wilayah_tugas')) {
                $table->string('wilayah_tugas', 100)->nullable()->after('organisasi');
                $table->index('wilayah_tugas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'wilayah_tugas')) {
                $table->dropIndex(['wilayah_tugas']);
                $table->dropColumn('wilayah_tugas');
            }
        });
    }
};
