<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function callRecord()
    {
        return $this->hasMany(CallRecord::class);
    }

    public function weeklyRecord()
    {
        return $this->hasMany(WeeklyRecord::class);
    }
}
