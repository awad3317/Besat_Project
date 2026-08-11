<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Driver;
use App\Models\User;
use App\Repositories\ChatRepository;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    public function __construct(
        private ChatRepository $chatRepository,
        private ImageService $imageService
    ) {}

    /**
     * جلب قائمة الرسائل لمحادثة معينة وتحديث حالة القراءة
     */
    public function getMessages(Request $request, $conversationId)
    {
        try {
            $user = auth('sanctum')->user();
            $conversation = Conversation::findOrFail($conversationId);
            // التحقق من صلاحية وصول العميل أو السائق للمحادثة
            $isUser = get_class($user) === User::class && ($user->id === $conversation->user_id || $user->type === 'admin');
            $isDriver = get_class($user) === Driver::class && $user->id === $conversation->driver_id;
            if (!$isUser && !$isDriver) {
                return ApiResponseClass::sendError('غير مصرح لك بالوصول لبيانات هذه المحادثة', null, 403);
            }
            // تصفير العداد غير المقروء
            $this->chatRepository->markAsRead($conversation, $user);
            $messages = $conversation->messages()
                ->with('sender:id,name')
                ->orderBy('created_at', 'desc')
                ->paginate(30);
            return ApiResponseClass::sendResponse($messages, 'تم جلب الرسائل بنجاح');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الرسائل', $e->getMessage(), 500);
        }
    }

    /**
     * إرسال رسالة جديدة وبث الحدث فوراً عبر WebSockets (Reverb)
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'conversation_id' => ['required', 'exists:conversations,id'],
                'type'            => ['required', Rule::in(['text', 'image', 'audio', 'location'])],
                'body'            => ['required_if:type,text,location', 'nullable', 'string'],
                'attachment'      => ['required_if:type,image,audio', 'nullable', 'file', 'max:10240'],
                'metadata'        => ['nullable', 'array'],
            ]);

            if ($validator->fails()) {
                return ApiResponseClass::sendValidationError('فشل التحقق من البيانات', $validator->errors(), 422);
            }

            $user = auth('sanctum')->user();
            $conversation = Conversation::findOrFail($request->conversation_id);

            $isOwner = (get_class($user) === User::class && $conversation->user_id === $user->id)
                     || (get_class($user) === Driver::class && $conversation->driver_id === $user->id);

            if (!$isOwner) {
                return ApiResponseClass::sendError('غير مصرح لك بالإرسال في هذه المحادثة', [], 403);
            }
            $data = $validator->validated();
            if ($request->hasFile('attachment')) {
                $folder = $data['type'] === 'image' ? 'chat/images' : 'chat/audio';
                $data['attachment_path'] = $this->imageService->saveImage($request->file('attachment'), $folder);
            }
            $message = $this->chatRepository->storeMessage($conversation, $user, $data);
           broadcast(new MessageSent($message));
            return ApiResponseClass::sendResponse($message->load('sender:id,name'), 'تم إرسال الرسالة بنجاح');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء إرسال الرسالة', $e->getMessage(), 500);
        }
    }

    /**
     * فتح محادثة الدعم الفني للمستخدم الحالي
     */
    public function openSupportChat(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (get_class($user) !== User::class) {
                return ApiResponseClass::sendError('هذه الخدمة متاحة للمستخدمين فقط', null, 403);
            }
            $conversation = $this->chatRepository->getOrCreateSupportConversation($user->id);
            return ApiResponseClass::sendResponse($conversation, 'محادثة الدعم الفني جاهزة');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('فشل فتح محادثة الدعم', $e->getMessage(), 500);
        }
    }

    /**
     * فتح أو جلب محادثة طلب (بين المستخدم والسائق)
     */
    public function openOrderChat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => ['required', 'exists:requests,id'],
                'driver_id'  => ['required', 'exists:drivers,id'],
            ]);

            if ($validator->fails()) {
                return ApiResponseClass::sendValidationError('بيانات الطلب غير صالحة', $validator->errors(), 422);
            }
            $user = auth('sanctum')->user();
            $conversation = $this->chatRepository->getOrCreateOrderConversation(
                $request->request_id,
                $user->id,
                $request->driver_id
            );
            return ApiResponseClass::sendResponse($conversation, 'محادثة الطلب جاهزة');
        } catch (Exception $e) {
            Log::error('Error opening order chat: ' . $e->getMessage());
            return ApiResponseClass::sendError('فشل فتح محادثة الطلب', $e->getMessage(), 500);
        }
    }
}