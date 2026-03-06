<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'user_role', // 'admin', 'client', 'technician'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
