<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
    {{-- رأس القسم وشريط البحث --}}
    <div class="flex flex-col gap-4 mb-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                سجل استخدام <span class="text-warning-500 dark:text-warning/90">الكوبون</span>
            </h3>
        </div>

        <div class="w-full sm:w-80">
            <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">البحث</label>
            <div class="relative mt-1">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="px-4 py-2.5 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="ابحث باسم المستخدم أو رقم الهاتف..." />
                <div class="flex absolute top-0 bottom-0 left-0 items-center px-3">
                    <div wire:loading wire:target="search">
                        <div class="w-5 h-5 rounded-full border-2 animate-spin border-brand-500 border-t-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الجدول --}}
    <div wire:loading.class="opacity-50" wire:target="search, nextPage, previousPage, gotoPage"
        class="overflow-x-auto max-w-full table-responsive-container">
        <table class="min-w-full">
            <thead>
                <tr class="border-gray-100 border-y dark:border-gray-800">
                    <th class="py-3 text-center">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم المستخدم</p>
                    </th>
                    <th class="py-3">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">اسم المستخدم</p>
                    </th>
                    <th class="py-3 text-center">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الهاتف</p>
                    </th>
                    <th class="py-3 text-center">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">عدد الاستخدامات</p>
                    </th>
                    <th class="py-3 text-center">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">تاريخ الاستخدام</p>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->users as $user)
                    @php
                        $cleanPhone = preg_replace('/_deleted_\d+$/', '', $user->phone ?? '');
                        $usedAt = $user->pivot->created_at
                            ? \Carbon\Carbon::parse($user->pivot->created_at)->format('Y/m/d h:i A')
                            : '—';
                    @endphp
                    <tr wire:key="coupon-user-{{ $user->id }}">
                        <td class="py-3">
                            <p class="text-center text-gray-500 text-theme-sm dark:text-gray-400">
                                {{ $user->id }}
                            </p>
                        </td>
                        <td class="py-3">
                            <div class="flex gap-1.5 items-center">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                    {{ $user->name }}
                                </p>
                                @if ($user->trashed())
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">
                                        محذوف
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            <p class="text-gray-500 text-theme-sm dark:text-gray-400" dir="ltr">
                                {{ $cleanPhone ?: '—' }}
                            </p>
                        </td>
                        <td class="py-3 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 font-medium rounded-full bg-brand-50 text-theme-xs text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                {{ $user->pivot->usage_count ?? 1 }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            <p class="inline-block px-2 py-0.5 font-medium text-gray-600 bg-gray-100 rounded-full text-theme-xs dark:bg-gray-800 dark:text-gray-400"
                                dir="ltr">
                                {{ $usedAt }}
                            </p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            {{ trim($search) !== '' ? 'لا توجد نتائج تطابق بحثك الحالي.' : 'لم يتم استخدام هذا الكوبون من قبل أي مستخدم حتى الآن.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- روابط التنقل بين الصفحات --}}
    @if ($this->users->hasPages())
        <div class="px-4 py-3 mt-4 border-t border-gray-200 dark:border-gray-700">
            {{ $this->users->links() }}
        </div>
    @endif
</div>
