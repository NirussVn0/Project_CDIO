<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessageDelete extends Model
{
    use HasFactory;

    protected $table = 'chat_message_deletes';

    public $timestamps = false;

    protected $fillable = [
        'message_id',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'id_nguoi_dung');
    }
}
