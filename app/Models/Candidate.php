<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Candidate extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "candidates";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'type',
        'status',
        'desc',
        'image',
        'coin',
        'password',
        'google_id',
        'main_cv',
        'find_job',
        'experience_id',
        'district_id',
        'desired_salary',
        'major',
        'date_to_top',
        'status_to_top',
        'remember_token',
        'email_verified_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
