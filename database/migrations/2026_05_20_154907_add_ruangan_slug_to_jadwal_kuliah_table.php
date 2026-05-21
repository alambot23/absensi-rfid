

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->string('ruangan_slug')
                ->nullable()
                ->after('ruangan')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->dropColumn('ruangan_slug');
        });
    }
};