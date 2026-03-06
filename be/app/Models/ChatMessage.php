<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'type',
        'content',
        'image_path',
        'recalled_at',
    ];

    protected $casts = [
        'recalled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(NguoiDung::class, 'sender_id', 'id_nguoi_dung');
    }

    public function deletes()
    {
        return $this->hasMany(ChatMessageDelete::class, 'message_id');
    }
}
