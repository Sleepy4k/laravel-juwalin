<x-layouts.portal title="Detail Container">

    @php $statusColor = $container->status->color(); @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-100 font-mono">{{ $container->hostname }}</h2>
                        <p class="text-sm text-gray-400 mt-1">
                            @if($container->ip_address) IP: <span class="font-mono text-gray-300">{{ $container->ip_address }}</span> @endif
                            @if($container->vmid) &bull; VMID: <span class="font-mono text-gray-300">{{ $container->vmid }}</span> @endif
                        </p>
                    </div>
                    <x-ui.badge id="status-badge" :color="$statusColor">{{ $container->status->label() }}</x-ui.badge>
                </div>
                <div class="grid grid-cols-3 gap-4 border-t border-gray-800 pt-4">
                    <div class="text-center bg-gray-900 rounded-lg py-3"><p class="text-2xl font-bold text-brand-400">{{ $container->cores }}</p><p class="text-xs text-gray-500 mt-1">vCPU</p></div>
                    <div class="text-center bg-gray-900 rounded-lg py-3"><p class="text-2xl font-bold text-brand-400">{{ round($container->memory_mb/1024,1) }} GB</p><p class="text-xs text-gray-500 mt-1">RAM</p></div>
                    <div class="text-center bg-gray-900 rounded-lg py-3"><p class="text-2xl font-bold text-brand-400">{{ $container->disk_gb }} GB</p><p class="text-xs text-gray-500 mt-1">Disk</p></div>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t border-gray-800 pt-4 text-sm">
                    <div><p class="text-gray-500">OS Template</p><p class="text-gray-200">{{ $container->os_template ?? 'Tidak diset' }}</p></div>
                    <div><p class="text-gray-500">Node</p><p class="text-gray-200">{{ $container->node ?? 'Tidak diset' }}</p></div>
                    <div><p class="text-gray-500">Dibuat</p><p class="text-gray-200">{{ $container->created_at->format('d M Y') }}</p></div>
                    <div><p class="text-gray-500">Berakhir</p><p class="{{ $container->expires_at?->isPast() ? 'text-red-400' : 'text-gray-200' }}">{{ $container->expires_at ? $container->expires_at->format('d M Y') : 'Belum diset' }}</p></div>
                </div>
            </div>
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                    <h3 class="font-semibold text-gray-100">Port Forwarding</h3>
                    <form method="POST" action="{{ route('portal.ports.store', $container) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="protocol" class="input py-1 text-xs"><option value="tcp">TCP</option><option value="udp">UDP</option></select>
                        <input type="number" name="container_port" placeholder="Port CT" min="1" max="65535" class="input py-1 text-xs w-24"/>
                        <input type="text" name="description" placeholder="Keterangan" class="input py-1 text-xs w-28"/>
                        <button type="submit" class="btn-primary btn-sm">+ Tambah</button>
                    </form>
                </div>
                @if($container->portForwarding && $container->portForwarding->count())
                <table class="table">
                    <thead><tr><th>Proto</th><th>Host Port</th><th>CT Port</th><th>Keterangan</th><th></th></tr></thead>
                    <tbody>
                        @foreach($container->portForwarding as $pf)
                        <tr>
                            <td class="uppercase text-xs font-mono text-gray-400">{{ $pf->protocol }}</td>
                            <td class="font-mono text-gray-200">{{ $pf->host_port }}</td>
                            <td class="font-mono text-gray-200">{{ $pf->container_port }}</td>
                            <td class="text-xs text-gray-400">{{ $pf->description ?? 'Tidak ada' }}</td>
                            <td>
                                <form method="POST" action="{{ route('portal.ports.destroy', [$container, $pf]) }}" data-confirm-form="Hapus port ini?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada port forwarding dikonfigurasi.</div>
                @endif
            </div>
        </div>
        <div class="space-y-4">
            @if($container->vmid && ! $container->isProvisioning())
            <div class="card card-body space-y-3">
                <h3 class="font-semibold text-gray-100 mb-2">Kelola Container</h3>

                {{-- Console button — only available when running --}}
                <a id="btn-console"
                   href="{{ route('portal.containers.console', $container) }}"
                   target="_blank"
                   class="btn-primary btn-sm w-full block text-center {{ $container->status->value !== 'running' ? 'opacity-50 pointer-events-none' : '' }}">
                    ⌨ Buka Console
                </a>

                <form method="POST" action="{{ route('portal.containers.start', $container) }}">
                    @csrf
                    <button id="btn-start" type="submit" class="btn-primary btn-sm w-full"
                        {{ $container->status->value === 'running' ? 'disabled' : '' }}>▶ Start</button>
                </form>
                <form method="POST" action="{{ route('portal.containers.stop', $container) }}">
                    @csrf
                    <button id="btn-stop" type="submit" class="btn-secondary btn-sm w-full"
                        {{ $container->status->value !== 'running' ? 'disabled' : '' }}>■ Stop</button>
                </form>
                <form method="POST" action="{{ route('portal.containers.restart', $container) }}">
                    @csrf
                    <button id="btn-restart" type="submit" class="btn-ghost btn-sm w-full"
                        {{ $container->status->value !== 'running' ? 'disabled' : '' }}>↺ Restart</button>
                </form>
            </div>
            @elseif($container->status->value === 'provisioning')
            <div class="card card-body text-center">
                <div class="h-10 w-10 border-4 border-brand-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-sm text-gray-400">Container sedang diprovisioning...</p>
            </div>
            @endif
            @if($container->order)
            <div class="card card-body text-sm space-y-2 text-gray-400">
                <h3 class="font-semibold text-gray-100 mb-1">Info Pesanan</h3>
                <p>Paket: <span class="text-gray-200">{{ $container->order->package->name ?? 'Custom' }}</span></p>
                <p>Aktif sampai: <span class="{{ $container->expires_at?->isPast() ? 'text-red-400' : 'text-gray-200' }}">{{ $container->expires_at ? $container->expires_at->format('d M Y') : 'Tidak diset' }}</span></p>
                <a href="{{ route('portal.orders.show', $container->order_id) }}" class="block text-center btn-ghost btn-sm mt-2">Lihat Pesanan</a>
            </div>
            @endif
            <a href="{{ route('portal.containers.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

    {{-- Realtime status polling --}}
    @if($container->vmid)
    <script>
        (function () {
            const statusUrl      = '{{ route('portal.containers.status', $container) }}';
            const isProvisioning = {{ $container->isProvisioning() ? 'true' : 'false' }};
            const badgeEl        = document.getElementById('status-badge');
            const btnStart       = document.getElementById('btn-start');
            const btnStop        = document.getElementById('btn-stop');
            const btnRestart     = document.getElementById('btn-restart');
            const btnConsole     = document.getElementById('btn-console');

            const colorMap = {
                green:  ['bg-green-500/10', 'text-green-400', 'ring-green-500/20'],
                gray:   ['bg-gray-500/10',  'text-gray-400',  'ring-gray-500/20'],
                blue:   ['bg-blue-500/10',  'text-blue-400',  'ring-blue-500/20'],
                red:    ['bg-red-500/10',   'text-red-400',   'ring-red-500/20'],
                orange: ['bg-orange-500/10','text-orange-400','ring-orange-500/20'],
            };

            function applyStatus(data) {
                if (badgeEl) {
                    badgeEl.textContent = data.label;
                    Object.values(colorMap).flat().forEach(c => badgeEl.classList.remove(c));
                    (colorMap[data.color] || colorMap.gray).forEach(c => badgeEl.classList.add(c));
                }

                const isRunning = data.status === 'running';

                if (btnStart)   { btnStart.disabled   = isRunning; }
                if (btnStop)    { btnStop.disabled     = ! isRunning; }
                if (btnRestart) { btnRestart.disabled  = ! isRunning; }
                if (btnConsole) {
                    if (isRunning) {
                        btnConsole.classList.remove('opacity-50', 'pointer-events-none');
                    } else {
                        btnConsole.classList.add('opacity-50', 'pointer-events-none');
                    }
                }
            }

            async function poll() {
                try {
                    const res  = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    // If container was provisioning when page loaded and now it's done,
                    // reload the page so the action buttons are rendered by the server.
                    if (isProvisioning && data.status !== 'provisioning') {
                        window.location.reload();
                        return;
                    }

                    applyStatus(data);
                } catch (_) { /* network error — keep current UI */ }
            }

            // Poll faster while provisioning (every 5s), slower otherwise (every 8s)
            setInterval(poll, isProvisioning ? 5000 : 8000);
        })();
    </script>
    @endif

</x-layouts.portal>
