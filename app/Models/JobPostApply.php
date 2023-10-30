<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostApply extends Model
{
    use HasFactory;
    protected $table = 'job_post_apply';

    protected $fillable=['curriculum_vitae_id','job_post_id','evaluate','name','email','phone','status','candidate_id','qualifying_round_id','introduce'];
}
