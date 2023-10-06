<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function callRecord()
    {
        return $this->hasMany(CallRecord::class);
    }

    public function opdb()
    {
        return $this->hasMany(Opdb::class);
    }

    public function userInfo()
    {
        return $this->hasMany(UserInfo::class);
    }

    public function weeklyRecord()
    {
        return $this->hasMany(WeeklyRecord::class);
    }
}
