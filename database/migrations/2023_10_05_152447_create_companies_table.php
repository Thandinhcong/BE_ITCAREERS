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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 50);
            $table->string('address')->nullable();
            $table->date('founded_in')->comment('ngày thành lập')->nullable();
            $table->string('name')->nullable()->comment('Người dại diện');
            $table->string('office')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 12)->unique();
            $table->string('map')->nullable();
            $table->string('logo')->nullable();
            $table->string('link_web');
            $table->string('image_paper')->nullable();
            $table->longText('description')->nullable();
            $table->integer('coin')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('tax_code', 50)->nullable();
            $table->integer('status')->default(0)->comment('0:pending, 1:active ,2:block');
            $table->integer('company_size_max')->nullable();
            $table->integer('company_size_min')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
