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
        Schema::create('edu', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->double('gpa');
            $table->string('type_degree', 55);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('major');
            $table->bigInteger('profile_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edu');
    }
};
