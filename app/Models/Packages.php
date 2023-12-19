<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packages extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'packages';
    protected $fillable = ['title', 'coin', 'price', 'reduced_price', 'status', 'type_account'];
}