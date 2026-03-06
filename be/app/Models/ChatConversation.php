<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatConversation extends Model
{
    use HasFactory;

    protected $table = 'chat_conversations';

    protected $fillable = [
        'type',
        'title',
        'created_by',
        'last_message_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function members()
    {
        return $this->hasMany(ChatConversationMember::class, 'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'last_message_id');
    }
}
