@php
    $code = '419';
    $title = 'Page Expired';
    $message = 'Your session expired or the page took too long.';
    $hint = 'Please sign in again to continue.';
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m5-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';
@endphp

@include('errors.layout', compact('code', 'title', 'message', 'hint', 'icon'))
