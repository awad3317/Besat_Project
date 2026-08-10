@extends('layouts.app')
@section('title', 'المحادثات والمراسلة')
@section('Breadcrumb', 'إدارة المحادثات المباشرة')

@section('style')
<style>
    /* الحاوية الرئيسية بمرونة عادية لضمان القائمة يمين والشات يسار */
    .chat-wrapper {
        display: flex;
        flex-direction: row; /* القائمة يمين والشات يسار طبيعياً مع dir=rtl */
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

    /* كروت المستخدمين داخل القائمة */
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

    .chat-user-card:hover {
        background-color: rgba(243, 244, 246, 0.7);
    }

    .dark .chat-user-card:hover {
        background-color: rgba(255, 255, 255, 0.04);
    }

    .chat-user-card.active {
        background-color: rgba(60, 80, 224, 0.08);
        border-right: 4px solid #f59e0b;
    }

    .dark .chat-user-card.active {
        background-color: rgba(255, 255, 255, 0.08);
        border-right-color: #f59e0b;
    }

    /* فقاعات الرسائل */
    .chat-bubble-admin {
    background-color: #f59e0b; /* لون أصفر دافئ (Amber/Yellow) */
    color: #ffffff;             /* نص أبيض (أو يمكنك استخدام #1f2937 لنص غامق) */
    border-radius: 1rem 1rem 0rem 1rem;
    max-width: 75%;
    padding: 0.75rem 1rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

    .chat-bubble-user {
        background-color: #f3f4f6;
        color: #1f2937;
        border-radius: 1rem 1rem 1rem 0rem;
        max-width: 75%;
        padding: 0.75rem 1rem;
        border: 1px solid #e5e7eb;
    }

    .dark .chat-bubble-user {
        background-color: rgba(255, 255, 255, 0.05);
        color: #f3f4f6;
        border-color: #374151;
    }
</style>
@endsection

@section('content')

    <livewire:chat.index />

@endsection

@section('script')
@endsection