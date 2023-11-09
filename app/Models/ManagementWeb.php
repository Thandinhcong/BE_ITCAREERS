<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementWeb extends Model
{
    use HasFactory;
    protected $table = 'management_web';
    protected $fillable = ['id', 'logo', 'banner', 'name_web', 'company_name', 'address', 'email', 'phone', 'sdt_lienhe'];
}
