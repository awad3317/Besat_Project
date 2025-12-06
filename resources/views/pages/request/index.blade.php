@extends('layouts.app')
@section('title', 'إدارة الطلبات العامة')
@section('Breadcrumb', 'إدارة الطلبات العامة')
@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />

@endsection

@section('style')

@endsection

@section('content')
    <div class=" mx-auto max-w-(--breakpoint-2xl)">
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
            <div
                class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">1,250</h4>
                </div>
            </div>

           <div
                class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400"> الطلبات المؤكدة</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">1,250</h4>
                </div>
            </div>

            <div
                class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400"> الطلبات الجارية</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">1,250</h4>
                </div>
            </div>

             <div
                class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400"> الطلبات الملغية</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">1,250</h4>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection