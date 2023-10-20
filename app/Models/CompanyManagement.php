<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyManagement extends Model
{
    use HasFactory;
    protected $table = "companies";
    protected $fillable = [
        'company_name',
        'tax_code',
        'address',
        'founded_in',
        'name',
        'office',
        'email',
        'password',
        'phone',
        'map',
        'logo',
        'link_web',
        'image_paper',
        'desc',
        'coin',
        'status'
    ];
}
