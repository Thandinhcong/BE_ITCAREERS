<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeJobPost extends Model
{
    use HasFactory;
    protected $table =  "type_job_post";
    protected $fillable = ['id', 'name', 'salary', 'desc'];
}
