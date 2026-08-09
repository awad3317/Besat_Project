<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ConversationSessionService;

class TiyarAiController extends Controller
{
    protected ConversationSessionService $sessionService;

    public function __construct(ConversationSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handleWebhook(Request $request)
    {
        try {
            $data = $request->all();

            // استخراج رقم الهاتف ونص الرسالة من Evolution API
            $remoteJid = $data['data']['Message']['key']['remoteJid'] ?? null;
            $messageText = $data['data']['Message']['conversation'] 
                ?? $data['data']['Message']['extendedTextMessage']['text'] 
                ?? null;

            if (!$remoteJid || !$messageText) {
                return response()->json(['status' => 'ignored']);
            }

            // تنظيف رقم الهاتف وإزالة النطاق
            $phone = explode('@', $remoteJid)[0];

            // 1. استرجاع سياق المحادثة السابق
            $history = $this->sessionService->getHistory($phone);

            // 2. معالجة النص بواسطة الذكاء الاصطناعي
            $aiResponse = $this->processWithAi($messageText, $history);

            // 3. تحديث سياق المحادثة
            $this->sessionService->addMessage($phone, 'user', $messageText);
            $this->sessionService->addMessage($phone, 'assistant', $aiResponse);

            // 4. إرسال الرسالة إلى العميل عبر الواتساب
            $this->sendWhatsAppMessage($phone, $aiResponse);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("Tiyar AI Webhook Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function processWithAi(string $userMessage, array $history): string
    {
        $systemPrompt = <<<EOT
أنت المساعد الذكي الرسمي لشركة "تيار للحلول البرمجية" (Tiyar for Software Solutions).
مهمتك الرد على العملاء بلباقة واحترافية وبشكل مختصر يناسب الواتساب.

معلومات الشركة وقاعدة المعرفة:
1. الموقع الرسمي: https://tiyar.cc/
2. نظام مرسل (Morsel SaaS): نظام سحابي لإدارة الشحنات والخدمات اللوجستية (https://mursal.tiyar.cc/).
3. تطبيق بساط (Besat App): تطبيق جوال لخدمات النقل والتوصيل المباشر للأفراد والطلبات (مثل أوبر وكريم) داخل اليمن.
4. معرض أعمال لوفي بيبي (Luffy Baby): موقع معرض أعمال (https://1st-factory.com/) لشركة لوفي بيبي الرائدة في توريد الحفائض في اليمن والسعودية.
5. الخدمات البرمجية المخصصة: تطوير تطبيقات الجوال، المنصات السحابية، الأنظمة الإدارية، أتمتة الـ APIs، وإدارة السيرفرات.

قواعد سلوك الرد:
- عند التحية: رحب بالعميل بأسلوب محترف باسم شركة تيار.
- عند الاستفسار عن منتج/عمل سابق: قدم شرحاً مختصراً مع إرفاق الرابط المباشر إن وجد.
- عند طلب مشروع جديد: اطلب من العميل تزويدك بـ (اسم العميل، فكرة المشروع، الميزانية التقريبية، ورقم التواصل).
- عدم إعطاء أسعار نهائية للمشاريع المخصصة قبل مراجعة الفريق الفني.
EOT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.1-8b-instant',
            'messages' => $messages,
            'temperature' => 0.5,
            'max_tokens' => 400,
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'] ?? 'أهلاً بك في شركة تيار! كيف يمكننا مساعدتك اليوم؟';
        }

        Log::error("Groq AI Error: " . $response->body());
        return "أهلاً بك في شركة تيار للحلول البرمجية! يسعدنا تواصلك معنا، كيف يمكننا مساعدتك اليوم؟";
    }

    private function sendWhatsAppMessage(string $phone, string $text)
    {
        $baseUrl = rtrim(env('EVOLUTION_API_BASE_URL'), '/');
        $apiKey = env('EVOLUTION_API_KEY');

        Http::withHeaders([
            'apikey' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/send/text", [
            'number' => $phone,
            'text' => $text,
            'delay' => 1200,
            'options' => [
                'presence' => 'composing'
            ]
        ]);
    }
}