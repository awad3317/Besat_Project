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
    </style>
@endsection

@section('content')

    <livewire:chat.index />

@endsection

@section('script')
@endsection
