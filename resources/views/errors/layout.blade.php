<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-xl bg-white border border-slate-200 shadow-sm rounded-2xl p-8 space-y-6">
        <div class="text-center space-y-1">
            <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ config('app.name') }}</div>
            <div class="text-xs text-slate-400">HR Vacations</div>
        </div>

        <div class="text-center space-y-4">
            @isset($icon)
                <div class="flex justify-center">
                    <div class="h-12 w-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        {!! $icon !!}
                    </div>
                </div>
            @endisset

            @isset($code)
                <div class="text-sm font-semibold text-slate-500">{{ $code }}</div>
            @endisset

            <h1 class="text-3xl font-bold text-slate-900">
                {{ $title ?? 'Something went wrong' }}
            </h1>

            <div class="space-y-2 text-slate-600 leading-relaxed">
                @isset($message)
                    <p>{{ $message }}</p>
                @endisset
                @isset($hint)
                    <p class="text-slate-500 text-sm">{{ $hint }}</p>
                @endisset
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-center sm:space-x-3 space-y-2 sm:space-y-0">
            <a href="/" class="inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Go to Home / Login
            </a>
            <button type="button" onclick="history.back()" class="inline-flex justify-center rounded-lg border border-slate-300 px-4 py-2 text-slate-700 font-medium hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Go back
            </button>
        </div>
    </div>
</body>
</html>
