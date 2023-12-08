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
        Schema::table('profile', function (Blueprint $table) {
            $table->softDeletes();
            $table->longText('careers_goal')->nullable();
            $table->integer('type')->nullable()->comment('0: cv upload, 1: cv create');
            $table->json('coin_status')->nullable();
            $table->integer('percent_cv')->nullable();
            $table->integer('coin_exp')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};