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
            $table->string('phone')->nullable()->after('email_verified_at');
            $table->foreignId('church_id')->nullable()->after('phone')->constrained('churches')->nullOnDelete();
            $table->timestamp('last_login_at')->nullable()->after('church_id');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->softDeletes()->after('updated_at');

            $table->index('church_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['church_id']);
            $table->dropIndex(['church_id']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['phone', 'church_id', 'last_login_at', 'is_active', 'deleted_at']);
        });
    }
};
