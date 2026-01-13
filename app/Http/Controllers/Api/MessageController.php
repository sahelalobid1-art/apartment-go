<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * عرض قائمة المحادثات (Inbox)
     */
    public function conversations(Request $request)
    {
        $conversations = $this->messageService->getUserConversations($request->user()->id);

        return ConversationResource::collection($conversations);
    }

    /**
     * عرض الرسائل بين المستخدم الحالي ومستخدم آخر
     */
    public function getMessages(Request $request, $userId)
    {
        // $userId هنا هو الـ ID الخاص بالطرف الآخر في المحادثة
        $messages = $this->messageService->getConversationHistory($request->user()->id, $userId);

        return MessageResource::collection($messages);
    }

    /**
     * إرسال رسالة جديدة
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $message = $this->messageService->sendMessage($request->validated());

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => new MessageResource($message->load(['sender', 'receiver'])),
        ], 201);
    }

    /**
     * تحديد رسالة معينة كمقروءة
     */
    public function markAsRead(Message $message): JsonResponse
    {
        // نستخدم الـ Policy للتحقق
        Gate::authorize('markAsRead', $message);

        $this->messageService->markMessageAsRead($message);

        return response()->json(['message' => 'Message marked as read']);
    }
}
