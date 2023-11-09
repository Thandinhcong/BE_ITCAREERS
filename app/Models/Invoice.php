<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = ['id', 'user_id', 'package_id', 'status', 'amount', 'total', 'created_at', 'updated_at'];
    public function package()
    {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }
}
