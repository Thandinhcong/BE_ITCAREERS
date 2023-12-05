<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "edu";
    protected $fillable = [
        'name',
        'gpa',
        'type_degree',
        'start_date',
        'end_date',
        'major',
        'profile_id'
    ];
}
