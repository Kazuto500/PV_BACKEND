<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRecord extends Model
{
    use HasFactory;

    public function agentForm()
    {
        return $this->belongsTo(AgentForm::class);
    }
}
