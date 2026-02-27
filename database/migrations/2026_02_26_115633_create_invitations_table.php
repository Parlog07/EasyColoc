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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('colocation_id')->constrained()->onDelete('cascade');

            $table->string('email');
            $table->string('token')->unique();

            $table->string('status')->default('pending'); 
            // pending | accepted | refused | expired

            $table->timestamp('expires_at');

            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
