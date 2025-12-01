@extends('layouts.admin')

@section('title', 'Page Setting')

@section('content')
<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            @foreach($pages as $i => $page)
                <li class="nav-item">
                    <a
                      class="nav-link {{ $i === 0 ? 'active' : '' }}"
                      id="custom-tabs-{{ $page->id }}-tab"
                      data-toggle="pill"
                      href="#custom-tabs-{{ $page->id }}"
                      role="tab"
                      aria-controls="custom-tabs-{{ $page->id }}"
                      aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                      {{ $page->name ?? $page->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="custom-tabs-four-tabContent">
            @foreach($pages as $i => $page)
                @php
                    // CONTENT xử lý
                    $contentRaw = $page->content ?? '';

                    if (is_array($contentRaw)) {
                        // dạng {"html":"..."}
                        $contentRaw = $contentRaw['html'] ?? json_encode($contentRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                    }

                    if (!is_string($contentRaw)) {
                        $contentRaw = '';
                    }

                    // FEATURES xử lý
                    $features = $page->features ?? [];
                    $counterItems = [];

                    for ($k = 0; $k < 4; $k++) {
                        $counterItems[$k] = [
                            'icon' => $features[$k]['icon'] ?? '',
                            'value' => $features[$k]['value'] ?? '',
                            'title' => $features[$k]['title'] ?? '',
                            'description' => $features[$k]['description'] ?? '',
                        ];
                    }
                @endphp

                <div
                  class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
                  id="custom-tabs-{{ $page->id }}"
                  role="tabpanel"
                  aria-labelledby="custom-tabs-{{ $page->id }}-tab">

                    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="mb-4">
                        @csrf
                        @method('PUT')

                        {{-- Title --}}
                        <div class="form-group">
                            <label for="title-{{ $page->id }}">Tiêu đề</label>
                            <input
                              type="text"
                              id="title-{{ $page->id }}"
                              name="title[{{ $page->id }}]"
                              class="form-control"
                              value="{{ old("title.{$page->id}", $page->title) }}"
                              placeholder="Enter Title">
                        </div>

                        {{-- Image --}}
                        <x-admin.form.media-input
                            id="image_original_path_{{ $page->id }}"  {{-- THÊM DÒNG NÀY --}}
    name="image_original_path[{{ $page->id }}]"
                            label="Ảnh đại diện"
                            :multiple="false"
                            :value="optional($page->mainImage())->original_path" />

                        <x-admin.form.media-input
                            id="banner_original_path_{{ $page->id }}" {{-- THÊM DÒNG NÀY --}}
    name="banner_original_path[{{ $page->id }}]"
                            label="Banner"
                            :multiple="false"
                            :value="optional($page->bannerImage())->original_path" />

                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description-{{ $page->id }}">Mô tả</label>
                            <textarea
                              id="description-{{ $page->id }}"
                              name="description[{{ $page->id }}]"
                              class="form-control"
                              rows="3"
                              placeholder="Enter description">{{ old("description.{$page->id}", $page->description ?? '') }}</textarea>
                        </div>

                        {{-- Content --}}
                        <div class="form-group">
                            <label for="content-{{ $page->id }}">Nội dung</label>
                            <textarea
                              id="content-{{ $page->id }}"
                              name="content[{{ $page->id }}]"
                              class="form-control"
                              rows="6"
                              placeholder="Nội dung">{{ old("content.{$page->id}", $contentRaw) }}</textarea>
                        </div>

                        {{-- COUNTER --}}
                        @if(isset($page->slug) && $page->slug === 'counter')
                            <div class="card card-outline card-body mb-3">
                                <h5 class="mb-3">Số đếm (4 items cố định)</h5>

                                @foreach($counterItems as $idx => $c)
                                    <div class="border rounded p-2 mb-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Icon #{{ $idx+1 }}</label>
                                                <input type="text"
                                                       name="features[{{ $page->id }}][{{ $idx }}][icon]"
                                                       class="form-control"
                                                       value="{{ old("features.{$page->id}.{$idx}.icon", $c['icon']) }}">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label>Value (số) #{{ $idx+1 }}</label>
                                                <input type="number"
                                                       name="features[{{ $page->id }}][{{ $idx }}][value]"
                                                       class="form-control"
                                                       value="{{ old("features.{$page->id}.{$idx}.value", $c['value']) }}">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Title #{{ $idx+1 }}</label>
                                                <input type="text"
                                                       name="features[{{ $page->id }}][{{ $idx }}][title]"
                                                       class="form-control"
                                                       value="{{ old("features.{$page->id}.{$idx}.title", $c['title']) }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Description #{{ $idx+1 }}</label>
                                            <input type="text"
                                                   name="features[{{ $page->id }}][{{ $idx }}][description]"
                                                   class="form-control"
                                                   value="{{ old("features.{$page->id}.{$idx}.description", $c['description']) }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
