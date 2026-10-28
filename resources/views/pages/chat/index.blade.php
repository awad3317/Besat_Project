@extends('layouts.app')
@section('title', 'المحادثات والمراسلة')

@section('style')
    <style>
        /* الحاوية الرئيسية بمرونة عادية لضمان القائمة يمين والشات يسار */
        .chat-wrapper {
            display: flex;
            flex-direction: row;
            /* القائمة يمين والشات يسار طبيعياً مع dir=rtl */
            gap: 1.25rem;
            height: calc(100vh - 160px);
            min-height: 600px;
            direction: rtl;
            width: 100%;
        }

        /* الشريط الجانبي (قائمة المحادثات والمستخدمين) */
        .chat-sidebar {
            width: 360px;
            min-width: 360px;
            display: flex;
            flex-direction: column;
            border-radius: 1rem;
            overflow: hidden;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        /* صندوق المحادثة الرئيسي (الجهة اليسرى) */
        .chat-main-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-radius: 1rem;
            overflow: hidden;
            min-width: 0;
            box-sizing: border-box;
        }

        /* شريط التمرير النظيف */
        .chat-custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .chat-custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 10px;
        }

        .chat-user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        /* تنسيق التابات في الوضع الفاتح والداكن */
        .chat-tabs-container {
            display: flex;
            padding: 4px;
            gap: 4px;
            background-color: #f2f4f7;
            border: 1px solid #e4e7ec;
            border-radius: 10px;
        }

        .dark .chat-tabs-container {
            background-color: #1d2939;
            border-color: #344054;
        }

        .chat-tab-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 7px;
            transition: all 0.2s ease;
            color: #667085;
            background-color: transparent;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .chat-tab-btn:hover {
            color: #1d2939;
            background-color: rgba(16, 24, 40, 0.04);
        }

        .dark .chat-tab-btn {
            color: #98a2b3;
        }

        .dark .chat-tab-btn:hover {
            color: #f2f4f7;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .chat-tab-btn.active {
            background-color: #ffffff;
            color: #dc6803;
            border-color: rgba(220, 104, 3, 0.15);
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
        }

        .dark .chat-tab-btn.active {
            background-color: #dc6803;
            color: #ffffff;
            border-color: #dc6803;
            box-shadow: 0 2px 8px rgba(220, 104, 3, 0.35);
        }

        .chat-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            font-size: 11px;
            font-weight: 700;
            line-height: 20px;
            border-radius: 5px;
            background-color: #dc6803;
            color: #ffffff;
        }

        .dark .chat-tab-btn.active .chat-tab-badge {
            background-color: #ffffff;
            color: #dc6803;
        }
    </style>
@endsection

@section('content')

    <livewire:chat.index />

@endsection

@section('script')
@endsection
