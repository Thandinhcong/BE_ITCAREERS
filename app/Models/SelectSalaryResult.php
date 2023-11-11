<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectSalaryResult extends Model
{
    use HasFactory;
    protected $table='select_salary_result';
    protected $fillable=['result_salary','min_salary','max_salary'];
}
