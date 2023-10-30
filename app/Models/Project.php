<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = "project";
    protected $fillable = [
        'project_name',
        'instructor',
        'start_date',
        'start_date',
        'desc',
        'phone_instructor',
        'email_instructor',
        'profile_id'
    ];
}
