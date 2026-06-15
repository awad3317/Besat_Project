@extends('layouts.app')
@section('title', 'المستخدمين')
@section('Breadcrumb', 'المستخدمين')
@section('addButton')
    @include('pages.admin.create-admin-modal')
    @include('pages.admin.edit-admin-modal')
    <x-modals.success-modal />
    <x-modals.error-modal />

@endsection
@section('style')

@endsection
@section('content')
    <div class=" mx-auto max-w-(--breakpoint-2xl)">
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
            <div class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if(!request('is_banned') == 1) border border-brand-500 dark:border-brand-500 @endif"
                onclick="window.location.href='{{ route('admins.index') }}'">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="24" height="24" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <rect x="27" y="11" width="2" height="4"></rect>
                        <rect x="3" y="11" width="2" height="4"></rect>
                        <rect x="20" y="20" width="2" height="2"></rect>
                        <rect x="10" y="20" width="2" height="2"></rect>
                        <path
                            d="M21,4H11A5.0059,5.0059,0,0,0,6,9V23a2.0023,2.0023,0,0,0,2,2v3h2V25H22v3h2V25a2.0027,2.0027,0,0,0,2-2V9A5.0059,5.0059,0,0,0,21,4Zm3,6,.0009,6H8V10ZM11,6H21a2.995,2.995,0,0,1,2.8157,2H8.1843A2.995,2.995,0,0,1,11,6ZM8,23V18H24.0012l.0008,5Z"
                            transform="translate(0 0)"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المسئولين</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $admins->count() }}</h4>
                </div>

            </div>

            <div class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if(request('is_banned') == 1) border border-brand-500 dark:border-brand-500 @endif"
                onclick="window.location.href='{{ route('admins.index') }}?is_banned=1'">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" fill="#dc6803" stroke="#dc6803">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>cancelled</title>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="add" fill="#dc6803" transform="translate(42.666667, 42.666667)">
                                    <path
                                        d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"
                                        id="Combined-Shape"> </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">المحظورين</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                        0
                    </h4>
                </div>
            </div>

            <div
                class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>

            <div
                class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>
        </div>
    </div>
    <div
        class="rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    المستخدمين
                </h3>
            </div>
        </div>

        <div class="w-full overflow-x-auto table-responsive-container">
            <!-- table start -->
            <table class="min-w-full">
                <!-- table header start -->
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800">
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    رقم المستخدم
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الصورة
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الإسم
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    رقم الواتس اب
                                </p>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    رقم الجوال
                                </p>
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الحظر
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center justify-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الإجراءات
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->
                <!-- table body start -->
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $loop->iteration }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="flex items-center gap-3">
                                        <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                            @if($admin->image)
                                                <img src="{{ url($admin->image) }}" alt="Admin Image"
                                                    class="h-full w-full object-cover" />
                                            @else
                                                <img src="{{ asset('assets/img/User_img.png') }}" alt="User Image"
                                                    class="h-full w-full object-cover" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="flex items-center gap-3">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $admin->name }}
                                        </p>
                                    </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        @if ($admin->whatsapp_number)
                                            {{ $admin->whatsapp_number  }}
                                        @else
                                            __
                                        @endif
                                    </p>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $admin->phone }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center">
                                    @if ($admin->is_banned)
                                        <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                            محظور
                                        </span>
                                    @else
                                        <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                            نشط
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center align-middle" x-data="{ openOptions: false }">
                                <div class="flex relative justify-center items-center">
                                    <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                                        class="actions-trigger-btn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="19" r="2"></circle>
                                        </svg>
                                    </button>

                                    <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                                        class="actions-dropdown-menu">

                                        <!-- عرض التفاصيل -->
                                        <a href="{{ route('admins.show', $admin->id) }}"
                                            class="actions-dropdown-item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض التفاصيل
                                        </a>

                                        <!-- تعديل البيانات -->
                                        <a href="{{ route('admins.edit', $admin->id) }}"
                                            class="actions-dropdown-item edit-data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل البيانات
                                        </a>

                                        <!-- الحظر / فك الحظر -->
                                        @if($admin->is_banned)
                                            <form action="{{ route('admins.toggle-ban', $admin->id) }}" method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="actions-dropdown-item text-success-600 dark:text-success-500 hover:bg-success-50 dark:hover:bg-success-950/20">
                                                    <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                    فك الحظر
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admins.toggle-ban', $admin->id) }}" method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                    <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                    حظر
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-2">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div
                                        class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                        <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z"
                                                fill="" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                        لا يوجد مسئولين حتى الآن
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    {{-- @endforeach --}}
                    <!-- table body end -->
                </tbody>
                <!-- table body end -->
            </table>
            <!-- table end -->
        </div>
    </div>
@endsection

@section('script')

@endsection