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
    Schema::create('memberships', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->onDelete('cascade');

        $table->foreignId('colocation_id')
            ->constrained()
            ->onDelete('cascade');

        $table->string('role')->default('member'); // owner or member

        $table->timestamp('joined_at')->nullable();
        $table->timestamp('left_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
