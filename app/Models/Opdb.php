<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opdb extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'opDataBase',
        'scrip',
        'otherDocs',
        'brief'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
