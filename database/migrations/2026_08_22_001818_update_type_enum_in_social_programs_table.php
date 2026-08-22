<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE social_programs MODIFY COLUMN type ENUM('pelatihan_kerja', 'pembagian_sembako', 'pelatihan', 'pemberian_makanan', 'kesehatan', 'pendidikan', 'lainnya') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE social_programs MODIFY COLUMN type ENUM('pelatihan_kerja', 'pembagian_sembako') NOT NULL");
    }
};
