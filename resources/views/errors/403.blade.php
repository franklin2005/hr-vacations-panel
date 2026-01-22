@php
    $code = '403';
    $title = 'Forbidden';
    $message = "You don’t have permission to view this page.";
    $hint = 'If you believe this is an error, sign in with an account that has access.';
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/></svg>';
@endphp

@include('errors.layout', compact('code', 'title', 'message', 'hint', 'icon'))
