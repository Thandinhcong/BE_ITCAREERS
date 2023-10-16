<?php

namespace App\Models;

<<<<<<< Updated upstream
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
>>>>>>> Stashed changes
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

<<<<<<< Updated upstream
// use Laravel\Sanctum\HasApiTokens;

class Company extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

=======
class Company extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
>>>>>>> Stashed changes
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
<<<<<<< Updated upstream
    protected $guard = 'company';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

=======
    protected $table = "companies";

    protected $fillable = [
      'name', 'email', 'password','phone','office','logo','address','desc','company_name','tax_code','founded_in','map','link_web','image_paper','coin',
  ];
>>>>>>> Stashed changes
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
<<<<<<< Updated upstream
}
=======
}
>>>>>>> Stashed changes
