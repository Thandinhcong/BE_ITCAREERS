<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'companies';
    protected $fillable = ['id', 'company_name', 'tax_code', 'address', 'founded_in', 'name', 'office', 'email', 'password', 'phone', 'map', 'logo', 'link_web', 'image_paper', 'coin', 'token', 'status'];
}
