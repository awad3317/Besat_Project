@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('content')
    <div class="max-w-screen-2xl mx-auto">
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <div
                class="flex flex-col items-start justify-between rounded-2xl border w-1/4 sm:w-[25%] lg:w-[43%] border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">

                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g id="User / Users">
                                <path id="Vector"
                                    d="M21 19.9999C21 18.2583 19.3304 16.7767 17 16.2275M15 20C15 17.7909 12.3137 16 9 16C5.68629 16 3 17.7909 3 20M15 13C17.2091 13 19 11.2091 19 9C19 6.79086 17.2091 5 15 5M9 13C6.79086 13 5 11.2091 5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13Z"
                                    stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="mt-5 w-full">
                    <span class="text-sm text-gray-500 dark:text-gray-400">المستخدمين</span>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white/90">100</h4>
                </div>
            </div>

            <div
                class="flex flex-col items-start justify-between rounded-2xl border w-1/4 sm:w-[25%] lg:w-[43%] border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" id="icon"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: none;
                                    }
                                </style>
                            </defs>
                            <title>bus</title>
                            <rect x="27" y="11" width="2" height="4"></rect>
                            <rect x="3" y="11" width="2" height="4"></rect>
                            <rect x="20" y="20" width="2" height="2"></rect>
                            <rect x="10" y="20" width="2" height="2"></rect>
                            <path
                                d="M21,4H11A5.0059,5.0059,0,0,0,6,9V23a2.0023,2.0023,0,0,0,2,2v3h2V25H22v3h2V25a2.0027,2.0027,0,0,0,2-2V9A5.0059,5.0059,0,0,0,21,4Zm3,6,.0009,6H8V10ZM11,6H21a2.995,2.995,0,0,1,2.8157,2H8.1843A2.995,2.995,0,0,1,11,6ZM8,23V18H24.0012l.0008,5Z"
                                transform="translate(0 0)"></path>
                            <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1"
                                width="32" height="32">
                            </rect>
                        </g>
                    </svg>
                </div>
                <div class="mt-5 w-full">
                    <span class="text-sm text-gray-500 dark:text-gray-400">السائقين</span>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white/90">50</h4>
                </div>
            </div>
            <div
                class="flex flex-col items-start justify-between rounded-2xl border  sm:w-[25%] lg:w-[43%] border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="24" height="24" viewBox="0 0 32 32" id="icon"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: none;
                                    }
                                </style>
                            </defs>
                            <title>ordinal</title>
                            <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                            <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1"
                                width="32" height="32">
                            </rect>
                        </g>
                    </svg>
                </div>
                <div class="mt-5 w-full">
                    <span class="text-sm text-gray-500 dark:text-gray-400">الطلبات</span>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white/90">1,250</h4>
                </div>
            </div>

            <div
                class="flex flex-col items-start justify-between rounded-2xl border sm:w-[25%] lg:w-[43%] border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="24" height="24" viewBox="0 0 32 32" id="icon"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: none;
                                    }
                                </style>
                            </defs>
                            <polygon points="23.586 13 21 10.414 21 6 23 6 23 9.586 25 11.586 23.586 13"></polygon>
                            <path
                                d="M22,18a8,8,0,1,1,8-8A8.0092,8.0092,0,0,1,22,18ZM22,4a6,6,0,1,0,6,6A6.0066,6.0066,0,0,0,22,4Z">
                            </path>
                            <path d="M8.63,18l7,6H30V22H16.37l-7-6H4V2H2V28a2.0025,2.0025,0,0,0,2,2H30V28H4V18Z"></path>
                            <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1"
                                width="32" height="32">
                            </rect>
                        </g>
                    </svg>
                </div>
                <div class="mt-5 w-full">
                    <span class="text-sm text-gray-500 dark:text-gray-400">الطلبات الحالية</span>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white/90">45</h4>
                </div>
            </div>
        </div>





    </div>
@endsection
