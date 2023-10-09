<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingForm extends Model
{
    use HasFactory;
    protected $table = "working_form";
    protected $fillable = ['id', 'working_form','description'];
    //
}
