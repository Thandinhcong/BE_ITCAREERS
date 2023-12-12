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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('email', 50)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('status')->default(1)->comment('0: pending, 1: active, 2: block');
            $table->integer('main_cv')->nullable();
            $table->integer('find_job')->default(0)->comment('0:tắt tự động tìm việc, 1:bật tự động tìm việc');
            $table->rememberToken();
            //Bỏ
            $table->string('address', 250)->nullable();
            $table->integer('gender')->nullable()->default(0)->comment('0: male, 1: female');
            $table->text('desc')->nullable();
            $table->integer('type')->default(0);
          
            $table->double('coin')->default(0);
            $table->date('date_to_top')->default( date(' 2000-1-1'));
            $table->boolean('status_to_top')->default(0)->comment('1:đẩy lên top,0:bình thường');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
