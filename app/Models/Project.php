<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "project";
    protected $fillable = [
        'id',
        'project_name',
        'instructor',
        'start_date',
        'end_date',
        'desc',
        'phone_instructor',
        'email_instructor',
        'profile_id'
    ];
}
