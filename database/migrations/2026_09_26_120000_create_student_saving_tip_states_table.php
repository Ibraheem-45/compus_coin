<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_tips', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('recommendation_key', 120)->nullable();
            $table->unique(['user_id', 'recommendation_key']);
        });

        Schema::create('student_saving_tip_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tip_id')->constrained('saving_tips', 'tip_id')->cascadeOnDelete();
            $table->boolean('is_dismissed')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'tip_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_saving_tip_states');

        Schema::table('saving_tips', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'recommendation_key']);
            $table->dropColumn(['category', 'recommendation_key']);
        });
    }
};