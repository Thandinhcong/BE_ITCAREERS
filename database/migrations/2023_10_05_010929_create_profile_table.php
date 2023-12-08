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
            $table->date('birth')->nullable();
            // $table->string('exp_id')->nullable();
            // $table->string('major')->nullable();
            // $table->string('edu_profile_id')->nullable();
            // $table->string('skill_profile')->nullable();
            // $table->string('academic_level_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->bigInteger('candidate_id')->nullable();
            //bổ sung
            $table->integer('total_exp')->default(0)->comment('tổng số năm kinh nghiệm');
            $table->integer('is_active')->default(0)->comment('0: All Cv, 1: CV main');
            $table->string('image')->nullable()->comment('Ảnh Cv');
            $table->double('coin')->default(0)->comment('giá trị cv');
            $table->string('path_cv')->nullable();
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
