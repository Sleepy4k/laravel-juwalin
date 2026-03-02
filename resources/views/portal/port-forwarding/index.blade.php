<x-layouts.portal title="Port Forwarding">
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Port Forwarding Requests</h1>
        <a href="{{ route('portal.port-forwarding.create') }}"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + New Request
        </a>
    </div>

    @forelse ($requests as $req)
        <div class="rounded-xl border border-gray-800 bg-gray-900 p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="font-mono text-sm text-white">
                        {{ strtoupper($req->protocol) }}
                        :{{ $req->source_port }} → {{ $req->container->hostname ?? 'N/A' }}:{{ $req->destination_port }}
                    </p>
                    @if ($req->reason)
                        <p class="text-xs text-gray-400">{{ Str::limit($req->reason, 100) }}</p>
                    @endif
                </div>

                @php
                    $statusColor = match ($req->status) {
                        'active'   => 'text-green-400',
                        'approved' => 'text-blue-400',
                        'rejected' => 'text-red-400',
                        'removed'  => 'text-gray-500',
                        default    => 'text-yellow-400',
                    };
                @endphp
                <span class="text-sm font-medium {{ $statusColor }}">
                    {{ ucfirst($req->status) }}
                </span>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-gray-800 bg-gray-900 p-12 text-center">
            <p class="text-gray-400">No port forwarding requests yet.</p>
        </div>
    @endforelse

    {{ $requests->links() }}
</div>

</x-layouts.portal>
