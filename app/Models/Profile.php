<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "profile";
    protected $fillable = [
        'id',
        'title',
        'name',
        'email',
        'phone',
        'birth',
        'major',
        'address',
        'candidate_id',
        'total_exp',
        'is_active',
        'image',
        'coin',
        'path_cv',
        'careers_goal',
        'coin_status',
        'percent_cv',
        'coin_exp',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
    public function profileSkill()
    {
        return $this->belongsTo(SkillProfile::class, 'id', 'profile_id');
    }
    public function education()
    {
        return $this->belongsTo(Edu::class, 'id', 'profile_id');
    }
    public function exp()
    {
        return $this->belongsTo(Experience::class, 'id', 'seeker_id');
    }
}