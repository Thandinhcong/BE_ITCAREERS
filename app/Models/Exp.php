<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exp extends Model
{
    use HasFactory;
    protected $table = "exp";
    protected $fillable = ['id', 'company_name', 'postion', 'start_date', 'end_date', 'major_id'];
}
