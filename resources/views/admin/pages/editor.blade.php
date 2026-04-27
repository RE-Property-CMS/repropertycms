@extends('admin.layouts.editor')

@section('editor-title', isset($page) ? 'Edit: ' . $page->title : 'New Page')

@section('gjs-init-data')
@if(isset($page) && $page->gjs_data)
    {{-- Re-load existing project data (full editor state) --}}
    projectData: {!! $page->gjs_data !!},
@elseif(isset($initialHtml) && $initialHtml)
    {{-- First edit of a system page: seed with current Blade-rendered HTML --}}
    components: @json($initialHtml),
@endif
@endsection
