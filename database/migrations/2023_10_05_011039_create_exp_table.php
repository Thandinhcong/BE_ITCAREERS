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
            $table->string('position', 55)->comment('vị trí');
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('exp');
    }
};
