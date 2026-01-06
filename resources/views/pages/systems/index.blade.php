@extends('layouts.app')

@section('title', 'إدارة النظام')
@section('Breadcrumb', 'إدارة النظام')

@section('style')

@endsection

@section('content')

<!-- ========= Elite Settings Page with Glow Tabs ========= -->
<div class="mx-auto max-w-7xl">
    <div
        x-data="{ activeTab: 'general' }"
        class="overflow-hidden rounded-lg bg-white shadow-default dark:bg-boxdark"
    >
        <!-- Tab Navigation -->
        <div class="border-b border-stroke px-7 dark:border-strokedark">
            <div class="flex gap-8">
                <!-- General Settings Tab Button -->
                <button
                    @click="activeTab = 'general'"
                    :class="{ 'text-primary active-tab-glow': activeTab === 'general' }"
                    class="relative py-4 text-sm font-medium text-slate-500 transition-colors hover:text-primary dark:text-slate-400 dark:hover:text-primary"
                >
                    الإعدادات العامة
                </button>

                <!-- Surcharges Tab Button -->
                <button
                    @click="activeTab = 'surcharges'"
                    :class="{ 'text-primary active-tab-glow': activeTab === 'surcharges' }"
                    class="relative py-4 text-sm font-medium text-slate-500 transition-colors hover:text-primary dark:text-slate-400 dark:hover:text-primary"
                >
                    قواعد التسعير
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- General Settings Panel -->
            <div x-show="activeTab === 'general'" x-transition>
                <div class="flex flex-col divide-y divide-stroke dark:divide-strokedark">
                    {{-- Paste your General Settings List here --}}
                    {{-- Example item: --}}
                    <div x-data="{ isEditing: false }">
                        <div x-show="!isEditing" class="flex items-center justify-between p-7 transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                             <div class="flex items-center gap-5">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M18 10h4m-4 0v4m0-4l-3 3m0 0l-3 3m0 0l-4 4m-3 3l3-3"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-black dark:text-white">نسبة العمولة</h5>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">النسبة المئوية للعمولة على العمليات.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">15%</span>
                                <button @click="isEditing = true" class="text-sm font-bold text-warning-500 transition-colors hover:text-warning-600 dark:text-warning-500/90 dark:hover:text-warning-500">تعديل</button>
                            </div>
                        </div>
                        <div x-show="isEditing" x-transition class="bg-slate-100 p-7 dark:bg-strokedark">
                            {{-- Editing form goes here --}}
                        </div>
                    </div>
                     {{-- ... other general settings items ... --}}
                </div>
            </div>

            <!-- Surcharges Panel -->
            <div x-show="activeTab === 'surcharges'" x-transition>
                <div class="p-7">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-black dark:text-white">إدارة الزيادات السعرية</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">قواعد التسعير الديناميكي للخدمات.</p>
                        </div>
                        <button class="rounded bg-primary py-2 px-5 text-sm font-medium text-white hover:bg-opacity-90">
                            إضافة قاعدة جديدة
                        </button>
                    </div>
                    <div class="mt-6 flex flex-col">
                        {{-- Surcharges Table from previous response --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ========= Elite Settings Page with Glow Tabs End ========= -->

@endsection

@section('script')
    {{-- Alpine.js handles all the interactivity. --}}
@endsection
