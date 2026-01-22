@php
    $code = '404';
    $title = 'Page Not Found';
    $message = "We couldn’t find the page you were looking for.";
    $hint = 'The link may be broken or the page may have been removed.';
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M4.93 19.07a10 10 0 1 1 14.14 0A10 10 0 0 1 4.93 19.07Z" /></svg>';
@endphp

@include('errors.layout', compact('code', 'title', 'message', 'hint', 'icon'))
