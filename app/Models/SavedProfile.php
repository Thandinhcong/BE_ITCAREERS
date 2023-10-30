<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedProfile extends Model
{
    use HasFactory;
    protected $table='saved_profile';
    protected $fillable=['company_id','curriculum_vitae'];
}
