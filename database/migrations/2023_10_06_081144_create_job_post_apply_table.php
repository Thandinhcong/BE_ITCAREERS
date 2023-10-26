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
            $table->bigInteger('profile_id');
            $table->string('evaluate')->nullable()->comment('đánh giá');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->bigInteger('candidate_id');

            $table->integer('status')->default(0)->comment('1: đã xem, 0: chưa xem');
            $table->integer('qualifying_round_id')->default('0')->comment('vòng hồ sơ');
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
