<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table = "profile";
    protected $fillable = [
        'id',
        'title',
        'name',
        'email',
        'phone',
        'address',
        'image',
        'path_cv',
        'career_goal',
        'candidate_id',
        'major_id',
        'edu_id',
        'exp_id',
        'project_id',
    ];
}
