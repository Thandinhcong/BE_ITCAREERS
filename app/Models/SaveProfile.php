<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaveProfile extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='save_profile';
    protected $fillable=['company_id','candidate_id'];
}
