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
        Schema::create('job_post', function (Blueprint $table) {
            $table->id();
            $table->string('title')->fulltext();
            $table->integer('job_position_id');
            $table->integer('quantity')->comment('số lượng');
            $table->integer('academic_level_id')->comment('đại học, cao đẳng');
            $table->integer('exp_id')->comment('kinh nghiệm:1 năm, 2 năm');
            $table->integer('working_form_id');
            $table->double('min_salary');
            $table->double('max_salary');
            $table->text('requirement')->comment('yêu cầu công việc');
            $table->text('desc')->comment('Mô tả công việc')->fulltext();
            $table->text('interest')->comment('quyền lợi công việc');
            $table->integer('gender');
            $table->integer('area_id');
            $table->integer('major_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('company_id');
            $table->integer('view')->default(0);
            $table->integer('type_job_post_id')->default(0);
            $table->integer('status')->default(0)->comment('0:pending, 1:active, 2:block, 3:stop');
            $table->integer('admin_id')->nullable()->comment('id admin duyệt bài');
            $table->string('assess_admin')->nullable();
            $table->timestamps();
            $table->fullText(['title', 'desc']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post');
    }
};