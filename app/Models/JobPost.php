<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;
    protected $table = 'job_post';
    protected $fillable = [
        'id', 'title', 'job_position_id', 'exp_id', 'quantity', 'require', 'interest', 'min_salary', 'max_salary',
        'company_id', 'area_id', 'working_form_id', 'academic_level_id',  'major_id', 'gender',
        'start_date', 'end_date', 'status', 'district', 'province_id'
    ];
}
