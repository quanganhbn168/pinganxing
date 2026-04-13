@extends('layouts.master')
@section('title', ($pageSetting->intro_title ?? 'Về Chúng Tôi') . ' - ' . ($setting->site_name ?? config('app.name')))
@section('meta_description', strip_tags(Str::limit($pageSetting->intro_description ?? $pageSetting->intro_content ?? '', 160)))
@section('meta_image', !empty($pageSetting->intro_banner) ? asset($pageSetting->intro_banner) : (!empty($setting->share_image) ? asset($setting->share_image) : ''))
@section('og_type', 'website')
@section('content')

<x-frontend.page-hero 
    title="{{ $pageSetting->intro_title ?? 'Về Chúng Tôi' }}" 
    :breadcrumb="[['label' => 'Về Chúng Tôi']]" 
/>

<section class="w-full bg-white dark:bg-gray-900 border-b border-gray-100 min-h-screen">
    {{-- Khung làm việc (Canvas) cho trình soạn thảo WYSIWYG có thiết kế bằng Tailwind --}}
    <div class="cnetpos-wysiwyg-content w-full">
        {!! $pageSetting->intro_content ?? '<div class="text-center py-20 text-gray-500">Nội dung đang được cập nhật...</div>' !!}
    </div>
</section>

@endsection
