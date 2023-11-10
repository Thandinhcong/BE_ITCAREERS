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
        Schema::create('management_web', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('name_web', 50);
            $table->string('company_name', 50);
            $table->string('address');
            $table->string('email')->unique();
            $table->string('phone', 12);
            $table->string('sdt_lienhe', 12);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_web');
    }
};
