<div class="chat-wrapper" x-data="{
    currentChannelName: null,
    activeChannelObj: null,

    scrollToBottom() {
        setTimeout(() => {
            const container = document.getElementById('chatMessagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 150);
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
    <div class="bg-white border border-gray-200 chat-sidebar dark:border-gray-800 dark:bg-gray-900">

        <!-- الهيدر + إجمالي + البحث + التبويبات -->
        <div class="p-4 space-y-3 border-b border-gray-100 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white/90">
                    المحادثات المباشرة
                </h3>
                <span class="rounded-full bg-brand-500/10 px-2.5 py-0.5 text-[11px] font-bold text-brand-500">
                    إجمالي: {{ $totalCount }}
                </span>
            </div>
        </div>

        <!-- قائمة المحادثات -->
        <div id="sidebar-scroll" class="overflow-y-auto flex-1 p-3 space-y-2 custom-scrollbar">
            @forelse($conversations as $conv)
                @php
                    $participantName = $conv->user?->name ?? ($conv->driver?->name ?? 'مستخدم غير معروف');
                    $unreadCount = $conv->participant_unread_count ?? 0;
                    $isRequest = $conv->type === 'request';
                @endphp

                <div wire:key="conv-{{ $conv->id }}" wire:click="selectConversation({{ $conv->id }})"
                    class="chat-user-card group relative flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-200 {{ $selectedConversationId == $conv->id ? 'bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700' : 'hover:bg-white/60 dark:hover:bg-gray-800/60 border border-transparent' }}">

                    @if ($selectedConversationId == $conv->id)
                        <div class="absolute right-0 top-1/2 w-1 h-8 rounded-l-full -translate-y-1/2 bg-brand-500">
                        </div>
                    @endif

                    <!-- صورة / رمز العميل -->
                    <div class="relative flex-shrink-0 w-11 h-11">
                        <div
                            class="flex h-full w-full items-center justify-center rounded-full {{ $selectedConversationId == $conv->id ? 'bg-brand-500 text-white' : 'bg-brand-500/10 text-brand-500' }} font-bold border border-brand-500 text-sm shadow-sm transition-colors">
                            {{ mb_substr($participantName, 0, 1) }}
                        </div>
                    </div>

                    <!-- تفاصيل المحادثة -->
                    <div class="flex-1 pr-1 min-w-0">
                        <!-- السطر العلوي: الاسم + بادج عدد الرسائل غير المقروءة + الوقت -->
                        <div class="flex gap-2 justify-between items-start">
                            <!-- النصوص: الاسم + آخر رسالة -->
                            <div class="flex-1 min-w-0">
                                <h5
                                    class="mb-1 text-xs font-bold leading-tight text-gray-800 truncate dark:text-white/90">
                                    {{ $participantName }}
                                </h5>
                                <p class="text-[11px] text-gray-500 truncate dark:text-gray-400 leading-normal">
                                    {{ $conv->lastMessage?->body ?? 'لا يوجد رسائل' }}
                                </p>
                            </div>
                            <!-- الوقت + بادج -->
                            <!-- الوقت + بادج -->
                            <div class="flex flex-col flex-shrink-0 gap-1 items-end pt-0.5">
                                <span class="text-[8px] text-gray-400 font-medium">
                                    {{ $conv->last_message_at ? $conv->last_message_at->locale('ar')->translatedFormat('h:i A') : '' }}
                                </span>

                                <!-- تم تعديل اسم المتغير هنا -->
                                @if ($conv->participant_unread_count > 0)
                                    <span style="min-width: 20px;"
                                        class="flex h-5 items-center justify-center rounded-full bg-error-500 px-2 text-[10px] font-bold text-white shadow-sm shadow-error-500/30">
                                        {{ $conv->participant_unread_count > 99 ? '+99' : $conv->participant_unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-xs text-center text-gray-400">
                    لا توجد محادثات متاحة.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ====== 2. صندوق الشات الرئيسي (جهة اليسار) ====== -->
    <div class="bg-white border border-gray-200 chat-main-box dark:border-gray-800 dark:bg-gray-900">
        @if ($selectedConversation)

            <!-- هيدر الشات -->
            <div
                class="flex sticky top-0 z-10 justify-between items-center px-6 py-4 border-b border-gray-200 shadow-sm backdrop-blur-sm dark:border-gray-800 bg-white/95 dark:bg-gray-900/95">
                <!-- معلومات المستخدم (جهة اليمين) -->
                <a href="{{ $selectedConversation->user ? route('users.show', $selectedConversation->user->id) : ($selectedConversation->driver ? route('drivers.show', $selectedConversation->driver->id) : '#') }}"
                    class="flex gap-3 items-center min-w-0 transition-opacity hover:opacity-80">
                    <!-- دائرة اسم العميل مع منع الانكماش flex-shrink-0 -->
                    <div
                        class="flex flex-shrink-0 justify-center items-center w-11 h-11 text-sm font-bold rounded-full border shadow-sm bg-brand-500/10 text-brand-500 border-brand-500/20">
                        {{ mb_substr($selectedConversation->user?->name ?? ($selectedConversation->driver?->name ?? 'U'), 0, 1) }}
                    </div>

                    <!-- تفاصيل الاسم ورقم المحادثة -->
                    <div class="flex flex-col min-w-0 text-right">
                        <h5 class="text-sm font-bold text-gray-900 truncate dark:text-white">
                            {{ $selectedConversation->user?->name ?? $selectedConversation->driver?->name }}
                        </h5>

                    </div>
                </a>

                <!-- أزرار الإجراءات والإغلاق (جهة اليسار) -->
                <div class="flex flex-shrink-0 gap-2 items-center" x-data="{ confirmingClose: false }">

                    <template x-if="confirmingClose">
                        <div
                            class="flex gap-1 items-center p-1 rounded-lg border bg-error-50 border-error-200 dark:bg-error-950/30 dark:border-error-800">
                            <span class="text-[10px] text-error-600 dark:text-error-400 px-1 font-bold">تأكيد؟</span>
                            <button wire:click="closeConversation" type="button"
                                class="rounded bg-error-600 px-2 py-0.5 text-[10px] font-bold text-white hover:bg-error-700 cursor-pointer">نعم</button>
                            <button @click="confirmingClose = false" type="button"
                                class="rounded bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-200 cursor-pointer">إلغاء</button>
                        </div>
                    </template>

                    <button @click="$wire.set('selectedConversationId', null)" type="button" title="إغلاق الشاشة"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition cursor-pointer hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- سجل الرسائل -->
            <div id="chatMessagesContainer" wire:key="chat-messages-{{ $selectedConversation->id }}"
                class="overflow-y-auto relative flex-1 p-6 space-y-6 custom-scrollbar bg-gray-50/30 dark:bg-gray-900/40"
                x-data="{
                    pendingMessage: null,
                    scrollToBottom() {
                        setTimeout(() => { $el.scrollTop = $el.scrollHeight; }, 150);
                    }
                }" x-init="scrollToBottom()" @scroll-to-bottom.window="scrollToBottom()"
                @optimistic-message.window="pendingMessage = $event.detail.body; scrollToBottom()"
                @clear-pending.window="pendingMessage = null">

                @forelse($messages as $msg)
                    @php
                        $isAdmin =
                            ($msg->sender_type === \App\Models\User::class && $msg->sender_id == auth()->id()) ||
                            $msg->sender_type === 'admin';
                    @endphp

                    <div class="flex flex-col {{ $isAdmin ? 'items-start' : 'items-end' }} max-w-full">
                        <div
                            class="{{ $isAdmin ? 'bg-brand-500 text-white rounded-2xl rounded-tr-sm shadow-md shadow-brand-500/20' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-2xl rounded-tl-sm shadow-sm border border-gray-100 dark:border-gray-700' }} px-4 py-3 max-w-[85%] sm:max-w-[75%] relative group">
                            <p class="text-[14px] leading-relaxed break-words dark:text-white" dir="auto">
                                {{ $msg->body }}</p>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1.5 px-1 flex items-center gap-1">
                            {{ $msg->created_at ? $msg->created_at->locale('ar')->translatedFormat('h:i A') : '' }}
                            @if ($isAdmin)
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </span>
                    </div>
                @empty
                    <template x-if="!pendingMessage">
                        <div class="py-12 text-xs text-center text-gray-400">لا توجد رسائل سابقة في هذه المحادثة.</div>
                    </template>
                @endforelse

                <!-- الرسالة المؤقتة (Optimistic) -->
                <template x-if="pendingMessage">
                    <div class="flex flex-col items-start max-w-full animate-fade-in-up" style="opacity: 0.6;">
                        <div
                            class="bg-brand-500 text-white rounded-2xl rounded-tr-sm shadow-md shadow-brand-500/20 px-4 py-2.5 max-w-[85%] sm:max-w-[75%] relative">
                            <p class="text-[13px] leading-relaxed break-words" dir="auto" x-text="pendingMessage">
                            </p>
                        </div>
                        <span class="text-[9px] text-gray-400 mt-1 px-1 flex items-center gap-1">
                            <svg class="w-2.5 h-2.5 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                                    stroke-dasharray="31.4 31.4" stroke-linecap="round" />
                            </svg>
                            جاري الإرسال...
                        </span>
                    </div>
                </template>
            </div>

            <!-- حقل الإدخال -->
            <div class="p-4 bg-white border-t border-gray-200 dark:border-gray-800 dark:bg-gray-900"
                x-data="{
                    sendOptimistic() {
                        const input = this.$refs.msgInput;
                        const text = input.value.trim();
                        if (!text) return;
                        window.dispatchEvent(new CustomEvent('optimistic-message', { detail: { body: text } }));
                    }
                }">
                <form wire:submit.prevent="sendMessage" @submit="sendOptimistic()"
                    class="flex relative gap-4 items-end mx-auto max-w-4xl">
                    <div class="relative flex-1">
                        <input type="text" x-ref="msgInput" wire:model="newMessage"
                            placeholder="اكتب رسالتك للعميل..."
                            class="w-full h-14 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-6 text-[14px] text-gray-900 outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:text-white transition-all shadow-sm" />
                    </div>

                    <button type="submit"
                        class="flex flex-shrink-0 justify-center items-center w-12 h-12 text-white rounded-2xl shadow-md transition-all cursor-pointer bg-brand-500 hover:bg-brand-600 shadow-brand-500/30 active:scale-95">
                        <svg class="w-5 h-5 transform rtl:-scale-x-100" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <!-- الشاشة الافتراضية -->
            <div class="flex flex-col justify-center items-center p-8 h-full text-gray-400">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">اختر محادثة من القائمة المتاحة لبدء
                    المراسلة</p>
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

        Livewire.hook('commit', ({
            succeed
        }) => {
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
