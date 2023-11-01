<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edu extends Model
{
    use HasFactory;
    protected $table = "edu";
    protected $fillable = [
        'name',
        'gpa',
        'type_degree',
        'start_date',
        'end_date',
        'major_id',
        'profile_id'
    ];
}
