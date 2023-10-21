<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateApply extends Model
{
    use HasFactory;
    protected $table = 'candidates';
    protected $fillable = ['id', 'name', 'phone', 'email', 'desc'];
}
