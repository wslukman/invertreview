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
        Schema::create('social_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['pelatihan_kerja', 'pembagian_sembako']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('capacity');
            $table->integer('registered_count')->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->string('image_path')->nullable();
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->timestamps();
            $table->softDeletes();

            $table->index('church_id');
            $table->index('type');
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_programs');
    }
};
