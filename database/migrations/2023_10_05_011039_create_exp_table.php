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
        Schema::create('exp', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 55);
            $table->string('postion', 55);
            $table->date('start_date');
            $table->date('end_date');
            $table->bigInteger('major_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp');
    }
};
