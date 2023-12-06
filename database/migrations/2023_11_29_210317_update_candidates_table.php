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
        Schema::table('candidates', function (Blueprint $table) {
            $table->integer('experience_id')->nullable()->comment('Kinh nghiệm');
            $table->double('district_id')->nullable()->comment('Địa điểm làm việc');
            $table->double('desired_salary')->nullable()->comment('Mức lương mong muốn');
            $table->string('major')->nullable();
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
