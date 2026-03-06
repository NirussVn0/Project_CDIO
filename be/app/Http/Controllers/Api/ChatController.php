<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Models\NguoiDung;

class ChatController extends Controller
{
    // Cư dân tạo/lấy chat với Ban Quản Lý
    public function startAdminChat(Request $request) {
        $user_id = $request->input('user_id');
        $user_role = $request->input('user_role');

        if (!$user_id || !$user_role) {
            return response()->json(['message' => 'Missing user info'], 400);
        }

        // Tìm conversation 1-1 giữa user này và admin
        $convUserId = ConversationUser::where('user_id', $user_id)
                        ->where('user_role', $user_role)
                        ->pluck('conversation_id');

        $conversation = Conversation::whereIn('id', $convUserId)->first();

        if (!$conversation) {
            // Tạo mới trò chuyện
            $conversation = Conversation::create([
                'title' => 'Trò chuyện hỗ trợ',
                'type' => 'direct'
            ]);

            // Add user
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user_id,
                'user_role' => $user_role
            ]);
            
            // Add admin đại diện (ví dụ là System hoặc Admin)
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => 1,
                'user_role' => 'admin'
            ]);
        }

        return response()->json(['conversation' => $conversation]);
    }

    // Danh sách các hội thoại
    public function listConversations(Request $request) {
        $user_id = $request->input('user_id');
        $user_role = $request->input('user_role');

        $convIds = ConversationUser::where('user_id', $user_id)
                        ->where('user_role', $user_role)
                        ->pluck('conversation_id');

        $conversations = Conversation::whereIn('id', $convIds)
                ->orderBy('updated_at', 'desc')
                ->get();

        // Format data
        $data = $conversations->map(function ($conv) use ($user_id, $user_role) {
            $lastMessage = Message::where('conversation_id', $conv->id)->orderBy('id', 'desc')->first();
            
            // Tìm người kia
            $otherUser = ConversationUser::where('conversation_id', $conv->id)
                ->where(function($q) use ($user_id, $user_role) {
                    $q->where('user_id', '!=', $user_id)->orWhere('user_role', '!=', $user_role);
                })->first();

            $personName = 'Ban Quản Lý';
            if ($otherUser && $otherUser->user_role == 'client') {
                $nd = NguoiDung::find($otherUser->user_id);
                $personName = $nd ? $nd->ho_ten : 'Khách hàng';
            } else if ($otherUser && $otherUser->user_role == 'admin') {
                $personName = 'Admin / Hỗ trợ';
            }

            return [
                'id' => $conv->id,
                'title' => $conv->title,
                'type' => $conv->type,
                'other_name' => $personName,
                'other_role' => $otherUser ? $otherUser->user_role : '',
                'last_message_preview' => $lastMessage ? $lastMessage->content : 'Bắt đầu trò chuyện...',
                'updated_at' => $conv->updated_at
            ];
        });

        return response()->json([
            'data' => $data,
            'has_more' => false
        ]);
    }

    // Danh sách tin nhắn trong trò chuyện
    public function listMessages($convId, Request $request) {
        $messages = Message::where('conversation_id', $convId)
                    ->orderBy('id', 'asc') // Vue component prepends if older, appends if new, in our fake mode it expects old to new
                    ->get()
                    ->map(function($m) use ($request) {
                        return [
                            'id' => $m->id,
                            'sender_name' => $m->sender_role == 'admin' ? 'Ban Quản Lý' : 'Bạn',
                            'content' => $m->content,
                            'type' => $m->type,
                            'created_at' => $m->created_at,
                            'is_me' => ($m->sender_id == $request->input('user_id') && $m->sender_role == $request->input('user_role')),
                            'recalled_at' => $m->recalled_at
                        ];
                    });

        return response()->json([
            'data' => $messages,
            'has_more' => false
        ]);
    }

    // Gửi tin nhắn text
    public function sendText($convId, Request $request) {
        $userId = $request->input('user_id');
        $userRole = $request->input('user_role');
        $content = $request->input('content');

        $message = Message::create([
            'conversation_id' => $convId,
            'sender_id' => $userId,
            'sender_role' => $userRole,
            'content' => $content,
            'type' => 'text'
        ]);

        Conversation::where('id', $convId)->update(['updated_at' => now()]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_name' => $userRole == 'admin' ? 'Ban Quản Lý' : 'Bạn',
                'content' => $message->content,
                'type' => $message->type,
                'created_at' => $message->created_at,
                'is_me' => true,
                'recalled_at' => null
            ]
        ]);
    }
}
