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
    Schema::create('churches', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->text('address');
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 11, 7)->nullable();
        $table->string('phone');
        $table->string('email');
        $table->text('description');
        $table->year('founded_year');
        
        // Status & Workflow
        $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
        $table->boolean('is_active')->default(false); // Untuk kontrol akses cepat
        
        // Audit Trail (Siapa yang submit & approve)
        $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('approved_at')->nullable();

        // Alasan (Dibutuhkan oleh logic Controller-mu)
        $table->text('rejection_reason')->nullable();
        $table->text('suspension_reason')->nullable();

        // Media
        $table->string('logo_path')->nullable();
        $table->string('cover_image_path')->nullable();
        
        $table->timestamps();
        $table->softDeletes();

        // Indexing untuk Performa
        $table->index('status');
        $table->index(['latitude', 'longitude']);
    });
}};