<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ConversationSessionService;
use Illuminate\Support\Str;

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

            Log::info("WhatsApp Webhook Received", $data);

            // 1. استخراج نص الرسالة ورقم العميل
            $messageText = $data['data']['Message']['conversation'] 
                ?? $data['data']['Message']['extendedTextMessage']['text'] 
                ?? null;

            $sender = $data['data']['Info']['Sender'] 
                ?? $data['data']['Info']['Chat'] 
                ?? null;

            if (!$sender || !$messageText) {
                return response()->json(['status' => 'ignored_empty']);
            }

            // تنظيف رقم الهاتف وإزالة النطاق
            $phone = explode('@', $sender)[0];

            // 2. تجنب الرسائل الصادرة من البوت/الموظف (IsFromMe) مع إمكانية إعادة التفعيل بكلمة السر
            $isFromMe = $data['data']['Info']['IsFromMe'] ?? false;
            if ($isFromMe) {
                if (Str::contains(mb_strtolower($messageText), 'تيار احبك')) {
                    $this->sessionService->disableHumanSupport($phone);
                    Log::info("AI reactivated by agent sending secret phrase for phone: {$phone}");
                }
                return response()->json(['status' => 'ignored_from_me']);
            }

            // 3. فحص هل أرسل العميل كلمة "تيار احبك" لإعادة تفعيل الـ AI؟
            if (Str::contains(mb_strtolower($messageText), 'تيار احبك')) {
                $this->sessionService->disableHumanSupport($phone);
                
                $welcomeBackMsg = "أهلاً بك مجدداً! ❤️ تم إعادة تفعيل المساعد الذكي لشركة تيار. كيف يمكننا مساعدتك اليوم؟";
                $this->sendWhatsAppMessage($phone, $welcomeBackMsg);

                Log::info("AI reactivated by customer secret phrase for phone: {$phone}");
                return response()->json(['status' => 'ai_reactivated']);
            }

            // 4. الفحص هل الرقم محوّل للدعم الفني البشري حالياً؟
            if ($this->sessionService->isHumanSupportActive($phone)) {
                Log::info("AI bypassed for {$phone}: Human support is active.");
                return response()->json(['status' => 'ignored_human_support_active']);
            }

            // 5. الفحص هل طلب العميل التحويل للدعم الفني؟
            if ($this->isRequestingHumanSupport($messageText)) {
                // تفعيل حالة الدعم الفني للرقم (إيقاف الـ AI)
                $this->sessionService->enableHumanSupport($phone);

                // إرسال التنبيه المحدد للعميل
                $transferMsg = "تم توجيهك إلى الدعم الفني لشركة تيار، سيتواصل معك أحد ممثلينا قريباً. 👨‍💻\n\nإذا أردت العودة والتواصل مع المساعد الآلي في أي وقت، أرسل كلمة: \"تيار احبك\"";
                $this->sendWhatsAppMessage($phone, $transferMsg);

                Log::info("Human support enabled and notification sent for phone: {$phone}");
                return response()->json(['status' => 'transferred_to_human']);
            }

            // 6. جلب سياق المحادثة لمعالجة الذكاء الاصطناعي
            $history = $this->sessionService->getHistory($phone);
            $aiResponse = $this->processWithAi($messageText, $history);

            // 7. حفظ المحادثة وإرسال الرد عبر الواتساب
            $this->sessionService->addMessage($phone, 'user', $messageText);
            $this->sessionService->addMessage($phone, 'assistant', $aiResponse);

            $this->sendWhatsAppMessage($phone, $aiResponse);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("Tiyar AI Webhook Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * فحص ما إذا كان نص الرسالة يحتوي على طلب تحويل للدعم الفني
     */
    private function isRequestingHumanSupport(string $text): bool
    {
        $keywords = [
            'دعم فني',
            'الدعم الفني',
            'خدمة العملاء',
            'خدمه العملاء',
            'تحدث مع موظف',
            'تكلم مع موظف',
            'تحويل لموظف',
            'تحويل للدعم',
            'اريد موظف',
            'أريد موظف',
            'كلمني موظف',
            'انساني',
            'شخص حقيقي',
            'مبيعات'
        ];

        foreach ($keywords as $keyword) {
            if (Str::contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function processWithAi(string $userMessage, array $history): string
    {
       $systemPrompt = <<<EOT
أنت المساعد الذكي الرسمي لشركة "تيار للحلول البرمجية" (Tiyar for Software Solutions).
مهمتك الرد على العملاء بلباقة واحترافية وبشكل مختصر يناسب المحادثات السريعة على الواتساب.

معلومات الشركة وقاعدة المعرفة (سابقة الأعمال والمنتجات):
1. الموقع الرسمي: https://tiyar.cc/
2. نظام مرسل (Mursal SaaS): نظام سحابي لإدارة الشحنات والخدمات اللوجستية (https://mursal.tiyar.cc/).
   - المجال: الشحن، اللوجستيات، إدارة الطرود، والكشوفات الآلية.
3. تطبيق بساط (Besat App): تطبيق جوال لخدمات النقل والتوصيل المباشر للأفراد والطلبات داخل اليمن.
   - رابط أندرويد (Google Play): https://play.google.com/store/apps/details?id=com.besat.app
   - رابط آيفون (App Store): https://apps.apple.com/app/id6758938790
   - المجال: تطبيقات النقل والتوصيل (مثل أوبر وكريم)، وحجز المشاوير.
4. معرض أعمال لوفي بيبي (loovy Baby): موقع معرض أعمال (https://1st-factory.com/) لشركة لوفي بيبي الرائدة في توريد الحفائض في اليمن والسعودية.
   - المجال: المواقع التعريفية، المعارض التجارية، والمنتجات الاستهلاكية.
5. الخدمات البرمجية والتصميمية المخصصة:
   - البرمجة: تطبيقات الجوال، المنصات السحابية، الأنظمة الإدارية، أتمتة الـ APIs، وإدارة السيرفرات.
   - التصميم: فريق محترف يبتكر الهويات البصرية الكاملة (Branding) وتصاميم المنشورات بأعلى المعايير الاحترافية.

قواعد سلوك الرد والربط الذكي (Smart Cross-Selling):
- عند التحية: رحب بالعميل بأسلوب محترف باسم "شركة تيار للحلول البرمجية".

- عند طلب العميل لمشروع جديد أو استفساره عن مجال معين:
  1. أظهر الحماس لفكرته، واعرض عليه فوراً المشروع المشابه الذي نفذته "تيار" كنموذج إثبات كفاءة (Proof of Concept).
     - مثال (إذا طلب تطبيق توصيل/تكسي): "رائع جداً! لدينا خبرة واسعة في هذا المجال، وقد قمنا بتطوير 'تطبيق بساط' لنقل الطلبات والأفراد [أرفق رابط المتجر]. يمكننا تنفيذ تطبيقك بميزات احترافية مشابهة وأعلى."
     - مثال (إذا طلب نظام شحن أو لوجستيات): "ممتاز! نحن متخصصون في الأنظمة اللوجستية، وقد طورنا 'نظام مرسل' السحابي لتسهيل إدارة الشحنات [أرفق رابط مرسل]."
     - مثال (إذا طلب تصميم هوية أو موقع): أظهر المزايا واعرض "موقع لوفي بيبي" كنموذج لتصميم المعارض.
  2. اطلب منه بأسلوب لطيف تزويدك بـ: (الاسم، تفاصيل المشروع المطلوب، الميزانية التقريبية، ورقم التواصل).

- قواعد عامة:
  - عدم إعطاء أسعار نهائية للمشاريع المخصصة قبل مراجعة الفريق الفني والتحليل.
  - حافظ على الردود قاطعة، واضحة، ولا تتجاوز فقرتين لضمان سهولة القراءة عبر الواتساب.
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