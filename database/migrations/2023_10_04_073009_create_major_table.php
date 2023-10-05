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
        Schema::create('major', function (Blueprint $table) {
            $table->id();
            $table->string('major', 55)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->string('company_name', 50);
            $table->string('tax_code', 50);
            $table->string('address')->nullable();
            $table->date('founded_in')->comment('ngày thành lập');
            $table->string('name');
            $table->string('office');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 12)->unique();
            $table->string('map')->nullable();
            $table->string('logo')->nullable();
            $table->string('link_web');
            $table->string('image_paper');
            $table->string('desc');
            $table->integer('coin')->default(0);
            $table->string('token');
            $table->integer('status')->default(0)->comment('0:pending, 1:active ,2:block');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('major');
    }
};
