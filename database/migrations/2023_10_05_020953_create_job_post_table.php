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
        Schema::create('job_post', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('job_position_id');
            $table->integer('exp_id');
            $table->integer('skill_id');
            $table->integer('quantity')->comment('số lượng');
            $table->integer('gender')->nullable()->comment('0: male, 1:female');
            $table->double('salary')->nullable();
            $table->integer('pay_form')->nullable();
            $table->string('require');
            $table->string('interest');
            $table->integer('level_id');
            $table->integer('company_id');
            $table->integer('area_id');
            $table->integer('working_form_id');
            $table->integer('academic_level_id');
            $table->integer('ranks_id')->comment('id cấp bậc');
            $table->integer('major_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('status')->comment('0:block, 1:active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post');
    }
};
