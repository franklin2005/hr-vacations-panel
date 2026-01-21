<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($this->getRequests() as $request)
            <div class="p-4 border rounded-xl">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">
                        {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                        →
                        {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                    </div>

                    <span
                        @class([
                            'px-2 py-1 rounded text-xs font-semibold',
                            'bg-yellow-100 text-yellow-800' => $request->status === 'pending',
                            'bg-green-100 text-green-800' => $request->status === 'approved',
                            'bg-red-100 text-red-800' => $request->status === 'rejected',
                        ])
                    >
                        {{ ucfirst($request->status) }}
                    </span>
                </div>

                <div class="text-sm text-gray-600 mt-1">
                    {{ $request->requested_days }} day(s)
                </div>

                @if ($request->reason)
                    <div class="text-sm mt-2 text-gray-500">
                        {{ $request->reason }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-sm text-gray-500">
                You have not submitted any vacation requests yet.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
