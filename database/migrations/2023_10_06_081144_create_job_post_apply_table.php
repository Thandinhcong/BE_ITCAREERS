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
        Schema::create('job_post_apply', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_post_id');
            $table->bigInteger('curriculum_vitae_id');
            $table->string('evaluate')->nullable()->comment('đánh giá');
            $table->string('introduce')->nullable()->comment('giới thiệu');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->bigInteger('candidate_id');
            $table->integer('status')->default(0)->comment('1: đã xem, 0: chưa xem');
            $table->integer('qualifying_round_id')->nullable()->comment('1:phù hợp, 0:không phù hợp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post_apply');
    }
};
