<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaveJobPost extends Model
{
    use HasFactory;
    protected $table='save_job_post';
    protected $fillable=['job_post_id','candidate_id'];
}
