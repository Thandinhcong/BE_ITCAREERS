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
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Untitled CV');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('birth')->nullable();
            $table->string('exp_id')->nullable();
            $table->string('major_id')->nullable();
            $table->string('edu_profile_id')->nullable();
            $table->string('skill_profile')->nullable();
            $table->string('academic_level_id')->nullable();
            $table->string('district_id', 55)->nullable();
            $table->bigInteger('candidate_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
