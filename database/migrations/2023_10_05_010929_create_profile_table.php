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
            $table->string('title',55);
            $table->date('bỉth');
            $table->string('address',55);
            $table->string('image',255);
            $table->string('path_cv',255);
            $table->string('career_goal',255);
            $table->bigInteger('candidate_id');
            $table->bigInteger('major_id');
            $table->bigInteger('edu_id');
            $table->bigInteger('exp_id');
            $table->bigInteger('project_id');

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
