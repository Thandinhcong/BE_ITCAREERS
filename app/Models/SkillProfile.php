<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "skill_profile";
    protected $fillable = ['id', 'profile_id', 'name_skill'];
    public function getNameSkill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'id');
    }
}
