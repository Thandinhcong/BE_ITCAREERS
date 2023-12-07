<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileOpen extends Model
{
    use HasFactory;
    protected $table='profile_open';
    protected $fillable=['company_id','coin','profile_id','start','comment'];
}
