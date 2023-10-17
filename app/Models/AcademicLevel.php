<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicLevel extends Model
{
    use HasFactory;
    protected $table = 'academic_level';
    protected $fillable = ['id', 'academic_level', 'description'];
}
