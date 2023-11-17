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
        'position',
        'start_date',
        'end_date',
        'desc',
        'link_project',
        'profile_id'
    ];
}