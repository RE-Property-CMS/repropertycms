{{--
  This view is no longer used for page creation.
  PagesController::create() now returns admin.pages.editor directly.
  Kept as a redirect fallback.
--}}
@php
    header('Location: ' . route('admin.pages.create'));
    exit;
@endphp
