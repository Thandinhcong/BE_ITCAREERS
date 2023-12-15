<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaJob extends Model
{
    use HasFactory;
    protected $table="area_job";
    protected $fillable=[
        'job_post_id',
        'province_id',
    ];
}
