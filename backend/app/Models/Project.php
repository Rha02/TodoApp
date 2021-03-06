<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Goal;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description'];

    public function goals()
    {
      return $this->hasMany(Goal::class);
    }

    public function members()
    {
      return $this->belongsToMany(User::class);
    }
}
