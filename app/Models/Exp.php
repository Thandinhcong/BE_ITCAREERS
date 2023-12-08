<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exp extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "exp";
    protected $fillable = ['id', 'company_name', 'position', 'start_date', 'end_date', 'profile_id', 'desc'];
}