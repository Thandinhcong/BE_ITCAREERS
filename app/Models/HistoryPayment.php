<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPayment extends Model
{
    use HasFactory;
    protected $table = 'history_payments';
    protected $fillable = ['id', 'user_id', 'note', 'coin', 'type_coin', 'type_account'];
}
