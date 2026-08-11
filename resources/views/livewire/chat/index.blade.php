<div class="chat-wrapper"
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

    <!-- ====== 1. القائمة الجانبية للمحادثات (جهة اليمين) ====== -->
    <div class="chat-sidebar border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        
        <!-- الهيدر + إجمالي + البحث + التبويبات -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-800 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white/90">
                    المحادثات المباشرة
                </h3>
                <span class="rounded-full bg-brand-500/10 px-2.5 py-0.5 text-[11px] font-bold text-brand-500">
                    إجمالي: {{ $totalCount }}
                </span>
            </div>
        </div>

        <!-- قائمة المحادثات -->
        <div id="sidebar-scroll" class="flex-1 overflow-y-auto p-2 chat-custom-scroll space-y-1.5">
    @forelse($conversations as $conv)
        @php
            $participantName = $conv->user?->name ?? $conv->driver?->name ?? 'مستخدم غير معروف';
            $unreadCount = $conv->participant_unread_count ?? 0;
            $isRequest = $conv->type === 'request';
        @endphp
        
        <div wire:key="conv-{{ $conv->id }}"
             wire:click="selectConversation({{ $conv->id }})"
             class="chat-user-card {{ $selectedConversationId == $conv->id ? 'active' : '' }}">
            
            <!-- صورة / رمز العميل -->
            <div class="relative h-11 w-11 flex-shrink-0">
                <div class="flex h-full w-full items-center justify-center rounded-full bg-brand-500/10 font-bold text-brand-500 border border-brand-500/20 text-xs shadow-xs">
                    {{ mb_substr($participantName, 0, 1) }}
                </div>
               
            </div>

            <!-- تفاصيل المحادثة -->
            <div class="flex-1 min-w-0 pr-1">
                <!-- السطر العلوي: الاسم + بادج عدد الرسائل غير المقروءة + الوقت -->
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <h5 class="text-xs font-bold text-gray-800 truncate dark:text-white/90 leading-tight">
                            {{ $participantName }}
                        </h5>
                        
                    </div>

                    <!-- الوقت -->
                    <span class="text-[10px] text-gray-400 font-medium flex-shrink-0">
                        {{ $conv->last_message_at ? $conv->last_message_at->format('h:i A') : '' }}
                    </span>
                </div>
                <!-- السطر السفلي: نص آخر رسالة + بادج غير مقروء -->
                <div class="flex items-center justify-between gap-1.5">
                    <p class="text-[11px] text-gray-500 truncate dark:text-gray-400 flex-1 leading-normal">
                        {{ $conv->lastMessage?->body ?? 'لا يوجد رسائل' }}
                    </p>
                    @if($unreadCount > 0)
                        <span class="flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-brand-500 px-1 text-[9px] font-bold text-white flex-shrink-0">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="p-8 text-center text-xs text-gray-400">
            لا توجد محادثات متاحة.
        </div>
    @endforelse
</div>
    </div>

    <!-- ====== 2. صندوق الشات الرئيسي (جهة اليسار) ====== -->
    <div class="chat-main-box border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        @if($selectedConversation)
            
            <!-- هيدر الشات -->
            <!-- هيدر المحادثة النشطة (معدل ومحاذى بدقة) -->
<div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs">
    
    <!-- معلومات المستخدم (جهة اليمين) -->
    <div class="flex items-center gap-3 min-w-0">
        <!-- دائرة اسم العميل مع منع الانكماش flex-shrink-0 -->
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-500/10 font-bold text-brand-500 border border-brand-500/20 text-sm">
            {{ mb_substr($selectedConversation->user?->name ?? $selectedConversation->driver?->name ?? 'U', 0, 1) }}
        </div>
        
        <!-- تفاصيل الاسم ورقم المحادثة -->
        <div class="flex flex-col min-w-0 text-right">
            <h5 class="text-xs font-bold text-gray-800 dark:text-white/90 truncate">
                {{ $selectedConversation->user?->name ?? $selectedConversation->driver?->name }}
            </h5>
           
        </div>
    </div>

    <!-- أزرار الإجراءات والإغلاق (جهة اليسار) -->
    <div class="flex items-center gap-2 flex-shrink-0" x-data="{ confirmingClose: false }">

        <template x-if="confirmingClose">
            <div class="flex items-center gap-1 rounded-lg bg-red-50 p-1 border border-red-200 dark:bg-red-950/30 dark:border-red-800">
                <span class="text-[10px] text-red-600 dark:text-red-400 px-1 font-bold">تأكيد؟</span>
                <button wire:click="closeConversation" type="button" class="rounded bg-red-600 px-2 py-0.5 text-[10px] font-bold text-white hover:bg-red-700 cursor-pointer">نعم</button>
                <button @click="confirmingClose = false" type="button" class="rounded bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-200 cursor-pointer">إلغاء</button>
            </div>
        </template>

        <button @click="$wire.set('selectedConversationId', null)" 
                type="button" 
                title="إغلاق الشاشة"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

            <!-- سجل الرسائل -->
            <div x-ref="messageContainer" class="flex-1 p-4 overflow-y-auto space-y-3.5 chat-custom-scroll bg-gray-50/50 dark:bg-gray-900/40">
                @forelse($messages as $msg)
                    @php
                        $isAdmin = ($msg->sender_type === \App\Models\User::class && $msg->sender_id == auth()->id()) || $msg->sender_type === 'admin';
                    @endphp
                    
                    <div class="flex flex-col {{ $isAdmin ? 'items-start' : 'items-end' }}">
                        <div class="{{ $isAdmin ? 'chat-bubble-admin' : 'chat-bubble-user' }}">
                            <p class="text-xs leading-relaxed break-words" dir="auto">{{ $msg->body }}</p>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 px-1">
                            {{ $msg->created_at ? $msg->created_at->format('H:i A') : '' }}
                        </span>
                    </div>
                @empty
                    <div class="text-center text-xs text-gray-400 py-12">لا توجد رسائل سابقة في هذه المحادثة.</div>
                @endforelse
            </div>

            <!-- حقل الإدخال -->
            <div class="p-3 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                    <input type="text" 
                           wire:model="newMessage"
                           placeholder="اكتب رسالتك للعميل..." 
                           class="h-10 flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 text-xs text-gray-800 outline-none focus:border-brand-500 dark:text-white/90" />
                    
                    <button type="submit" class="h-10 px-5 rounded-xl bg-brand-500 text-xs font-bold text-white hover:bg-brand-600 transition flex-shrink-0 cursor-pointer flex items-center gap-1">
                        <span>إرسال</span>
                    </button>
                </form>
            </div>

        @else
            <!-- الشاشة الافتراضية -->
            <div class="flex h-full flex-col items-center justify-center p-8 text-gray-400">
                <p class="text-xs font-medium">اختر محادثة من القائمة المتاحة لبدء المراسلة</p>
            </div>
        @endif
    </div>

</div>

@script
<script>
    let sidebarScrollPos = 0;
    const sidebarEl = document.getElementById('sidebar-scroll');

    if (sidebarEl) {
        sidebarEl.addEventListener('scroll', () => {
            sidebarScrollPos = sidebarEl.scrollTop;
        });
    }

    Livewire.hook('commit', ({ succeed }) => {
        const saved = sidebarScrollPos;
        succeed(() => {
            queueMicrotask(() => {
                const el = document.getElementById('sidebar-scroll');
                if (el) el.scrollTop = saved;
            });
        });
    });
</script>
@endscript