<div class="p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-[calc(100vh-80px)] font-outfit" dir="rtl"
     x-data="{ 
        currentChannelName: null,
        activeChannelObj: null,
        
        scrollToBottom() {
            $nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        },
        
        listenToChannel(conversationId, type) {
            if (!window.pusher) return;

            const prefix = type === 'request' ? 'chat.request.' : 'chat.support.';
            const newChannelName = prefix + conversationId;

            if (this.currentChannelName === newChannelName) return;

            if (this.currentChannelName && this.activeChannelObj) {
                window.pusher.unsubscribe(this.currentChannelName);
            }

            this.currentChannelName = newChannelName;
            this.activeChannelObj = window.pusher.subscribe(this.currentChannelName);

            this.activeChannelObj.bind('message.sent', (data) => {
                $wire.handleIncomingMessage();
            });
            
            this.activeChannelObj.bind('App\\Events\\MessageSent', (data) => {
                $wire.handleIncomingMessage();
            });
        }
     }"
     @subscribe-to-channel.window="listenToChannel($event.detail.conversationId, $event.detail.type)"
     @scroll-to-bottom.window="scrollToBottom()">

    <div class="max-w-[1400px] mx-auto">
        
        <!-- الحاوية الرئيسية الشاملة: مقسّمة إلى ديف يمين وديف يسار فقط -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden shadow-sm">
            <div class="grid grid-cols-12 h-[calc(100vh-140px)] min-h-[550px]">

                <!-- ==================== 1. الديف الأيمن: قائمة المستخدمين والمحادثات ==================== -->
                <div class="col-span-12 lg:col-span-4 border-b lg:border-b-0 lg:border-l border-gray-200 dark:border-gray-800 flex flex-col h-full bg-white dark:bg-gray-900">
                    
                    <!-- هيدر القائمة الجانبية -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">المستخدمين والمحادثات</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-500 dark:bg-brand-500/10">
                            {{ count($this->conversations) }}
                        </span>
                    </div>

                    <!-- قائمة عناصر المستخدمين -->
                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/60">
                        @forelse($this->conversations as $conv)
                            @php
                                $participantName = $conv->user?->name ?? $conv->driver?->name ?? 'مستخدم غير معروف';
                            @endphp
                            
                            <button wire:key="conv-{{ $conv->id }}" 
                                    wire:click="selectConversation({{ $conv->id }})"
                                    type="button"
                                    class="w-full text-right p-4 flex items-center gap-3 transition cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $selectedConversationId == $conv->id ? 'bg-brand-50/40 dark:bg-gray-800/90 border-r-4 border-brand-500' : '' }}">
                                
                                <!-- رمز/صورة المستخدم -->
                                <div class="relative flex-shrink-0">
                                    <div class="w-11 h-11 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-500 font-bold flex items-center justify-center text-lg">
                                        {{ mb_substr($participantName, 0, 1) }}
                                    </div>
                                    @if($conv->type === 'request')
                                        <span class="absolute -bottom-1 -right-1 bg-amber-500 text-white text-[9px] px-1.5 py-0.5 rounded-md font-bold">طلب</span>
                                    @endif
                                </div>

                                <!-- اسم المستخدم وآخر رسالة -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 truncate">{{ $participantName }}</h4>
                                        <span class="text-[11px] text-gray-400">
                                            {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">
                                        {{ $conv->lastMessage?->body ?? 'لا يوجد رسائل' }}
                                    </p>
                                </div>
                            </button>
                        @empty
                            <div class="p-8 text-center text-gray-400 text-sm">
                                لا يوجد مستخدمين أو محادثات متاحة.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- ==================== 2. الديف الأيسر: شاشة المحادثة النشطة ==================== -->
                <div class="col-span-12 lg:col-span-8 flex flex-col h-full bg-gray-50/50 dark:bg-gray-900/40">
                    @if($this->selectedConversation)
                        
                        <!-- 1. هيدر الشات (معلومات العميل وزر الإغلاق السريع بـ Alpine) -->
                        <div class="p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-500 font-bold flex items-center justify-center">
                                    {{ mb_substr($this->selectedConversation->user?->name ?? $this->selectedConversation->driver?->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white/90">
                                        {{ $this->selectedConversation->user?->name ?? $this->selectedConversation->driver?->name }}
                                    </h4>
                                    <span class="text-xs text-gray-400">محادثة #{{ $this->selectedConversation->id }} — النوع: {{ $this->selectedConversation->type == 'request' ? 'طلب' : 'دعم فني' }}</span>
                                </div>
                            </div>

                            <!-- أزرار الإغلاق والتحكم السريعة باستخدام Alpine.js -->
                            <div class="flex items-center gap-2" x-data="{ confirmingClose: false }">
                                <!-- زر إنهاء المحادثة -->
                                <template x-if="!confirmingClose">
                                    <button @click="confirmingClose = true" 
                                            type="button"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-800 transition cursor-pointer">
                                        إنهاء المحادثة
                                    </button>
                                </template>

                                <!-- تأكيد الإنهاء -->
                                <template x-if="confirmingClose">
                                    <div class="flex items-center gap-1.5 bg-red-50 dark:bg-red-950/30 p-1 rounded-lg border border-red-200 dark:border-red-800">
                                        <span class="text-[11px] text-red-600 dark:text-red-400 font-medium px-1">تأكيد؟</span>
                                        <button wire:click="closeConversation" 
                                                type="button"
                                                class="px-2 py-1 text-[11px] bg-red-600 text-white rounded font-bold hover:bg-red-700 transition cursor-pointer">
                                            نعم
                                        </button>
                                        <button @click="confirmingClose = false" 
                                                type="button"
                                                class="px-2 py-1 text-[11px] bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded font-bold hover:bg-gray-300 transition cursor-pointer">
                                            إلغاء
                                        </button>
                                    </div>
                                </template>

                                <!-- زر إغلاق نافذة المعاينة فورياً بالمتصفح بـ Alpine -->
                                <button @click="$wire.set('selectedConversationId', null)" 
                                        type="button"
                                        title="إغلاق المحادثة"
                                        class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- 2. منطقة عرض سجل الرسائل -->
                        <div x-ref="messageContainer" class="flex-1 p-5 overflow-y-auto space-y-4">
                            @forelse($this->messages as $msg)
                                @php
                                    $isAdmin = ($msg->sender_type === \App\Models\User::class && $msg->sender_id == auth()->id()) || $msg->sender_type === 'admin';
                                @endphp
                                <div class="flex flex-col {{ $isAdmin ? 'items-start' : 'items-end' }}">
                                    <div class="max-w-[75%] rounded-2xl p-4 text-sm {{ $isAdmin ? 'bg-brand-500 text-white rounded-br-none shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-none border border-gray-100 dark:border-gray-700/80 shadow-theme-xs' }}">
                                        <p class="leading-relaxed break-words">{{ $msg->body }}</p>
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 px-1">
                                        {{ $msg->created_at ? $msg->created_at->format('H:i A') : '' }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-gray-400 text-xs py-8">لا توجد رسائل سابقة في هذه المحادثة</div>
                            @endforelse
                        </div>

                        <!-- 3. حقل كتابة وإرسال الرسائل -->
                        <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
                            <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                                <input type="text" 
                                       wire:model="newMessage"
                                       placeholder="اكتب رسالتك هنا..." 
                                       class="flex-1 bg-gray-50 dark:bg-gray-800/80 text-gray-800 dark:text-white/90 text-sm rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-700 outline-none focus:border-brand-500 dark:focus:border-brand-500">
                                
                                <button type="submit" 
                                        class="bg-brand-500 hover:bg-brand-600 text-white font-medium text-sm px-6 py-3 rounded-xl transition shadow-theme-xs flex items-center gap-2 flex-shrink-0 cursor-pointer">
                                    <span>إرسال</span>
                                </button>
                            </form>
                        </div>

                    @else
                        <!-- الشاشة الافتراضية للجهة اليسرى قبل الضغط على أي مستخدم -->
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3 text-brand-500">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-.64-.025-1.279-.06-1.92-.107a48.275 48.275 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12A48.28 48.28 0 012.25 12.75v-2.142c0-.97.616-1.813 1.5-2.097M8.25 19.5a48.28 48.28 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium">اضغط على أحد المستخدمين من القائمة المجاورة لفتح المحادثة</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>