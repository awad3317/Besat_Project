<div class="p-4 md:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl"
     x-data="{ 
        currentChannel: null,
        scrollToBottom() {
            $nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        },
        listenToChannel(conversationId, type) {
            if (this.currentChannel) {
                pusher.unsubscribe(this.currentChannel);
            }
            const prefix = type === 'request' ? 'chat.request.' : 'chat.support.';
            this.currentChannel = prefix + conversationId;
            
            const channel = pusher.subscribe(this.currentChannel);
            channel.bind('message.sent', (data) => {
                $wire.handleIncomingMessage();
            });
        }
     }"
     @subscribe-to-channel.window="listenToChannel($event.detail.conversationId, $event.detail.type)"
     @scroll-to-bottom.window="scrollToBottom()">

    <div class="max-w-[1400px] mx-auto space-y-6">

        {{-- Stats Cards --}}
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-1">

            {{-- Total Conversations Card --}}
            <div wire:click="applyFilter('all')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('all')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-.64-.025-1.279-.06-1.92-.107a48.275 48.275 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12A48.28 48.28 0 012.25 12.75v-2.142c0-.97.616-1.813 1.5-2.097M8.25 19.5a48.28 48.28 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">إجمالي المحادثات</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] }}</h4>
                </div>
            </div>

            {{-- Support Conversations Card --}}
            <div wire:click="applyFilter('support')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'support') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('support')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 0012 20.25z" />
                    </svg>
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">محادثات الدعم الفني</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['support'] }}</h4>
                </div>
            </div>

            {{-- Request Conversations Card --}}
            <div wire:click="applyFilter('request')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'request') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('request')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v11.135m12 0a1.125 1.125 0 01-1.125 1.125H3.375" />
                    </svg>
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">محادثات الطلبات</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['request'] }}</h4>
                </div>
            </div>

        </div>

        {{-- Chat Interface Section --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
            <div class="grid grid-cols-12 h-[650px]">

                <!-- قائمة المحادثات الجانبية -->
                <div class="col-span-12 lg:col-span-4 border-b lg:border-b-0 lg:border-l border-gray-200 dark:border-gray-800 flex flex-col h-full">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-base font-bold text-gray-800 dark:text-white/90">المحادثات المتاحة</h3>
                    </div>

                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/60">
                        @forelse($this->conversations as $conv)
                            @php
                                $participantName = $conv->user?->name ?? $conv->driver?->name ?? 'مستخدم غير معروف';
                            @endphp
                            <button wire:key="conv-{{ $conv->id }}" 
                                wire:click="selectConversation({{ $conv->id }})"
                                class="w-full text-right p-4 flex items-center gap-3 transition cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $selectedConversationId == $conv->id ? 'bg-gray-50 dark:bg-gray-800/80 border-r-4 border-brand-500' : '' }}">
                                
                                <div class="relative flex-shrink-0">
                                    <div class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-brand-500 font-bold flex items-center justify-center">
                                        {{ mb_substr($participantName, 0, 1) }}
                                    </div>
                                    @if($conv->type === 'request')
                                        <span class="absolute -bottom-1 -right-1 bg-amber-500 text-white text-[9px] px-1.5 py-0.5 rounded-md font-bold">طلب</span>
                                    @endif
                                </div>

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
                                لا توجد محادثات قائمة.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- شباك الشات والتفاعل المباشر -->
                <div class="col-span-12 lg:col-span-8 flex flex-col h-full bg-gray-50/50 dark:bg-gray-900/40">
    @if($this->selectedConversation)
        <!-- 1. Header المحادثة (اسم العميل ورقم المحادثة) -->
        <div class="p-4 bg-white dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
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
        </div>

        <!-- 2. سجل الرسائل (Fetch Chat History) -->
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

        <!-- 3. حقل الإرسال -->
        <div class="p-4 bg-white dark:bg-gray-900/80 border-t border-gray-200 dark:border-gray-800">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                <input type="text" 
                       wire:model="newMessage"
                       placeholder="اكتب رسالتك للطرف الآخر هنا..." 
                       class="flex-1 bg-gray-50 dark:bg-gray-800/80 text-gray-800 dark:text-white/90 text-sm rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-700 outline-none focus:border-brand-500 dark:focus:border-brand-500">
                
                <button type="submit" 
                        class="bg-brand-500 hover:bg-brand-600 text-white font-medium text-sm px-6 py-3 rounded-xl transition shadow-theme-xs flex items-center gap-2 flex-shrink-0 cursor-pointer">
                    <span>إرسال</span>
                </button>
            </form>
        </div>
    @else
        <!-- الحالة الافتراضية قبل اختيار أي محادثة -->
        <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-.64-.025-1.279-.06-1.92-.107a48.275 48.275 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12A48.28 48.28 0 012.25 12.75v-2.142c0-.97.616-1.813 1.5-2.097M8.25 19.5a48.28 48.28 0 01-3.606-.412l-1.011.607a2.25 2.25 0 01-3.32-1.808v-1.12M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <p class="text-sm font-medium">اختر محادثة من القائمة الجانبية للبدء في التواصل</p>
        </div>
    @endif
</div>

            </div>
        </div>

    </div>
</div>