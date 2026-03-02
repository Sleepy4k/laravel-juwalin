<x-layouts.admin title="Detail Container">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-100 font-mono">{{ $container->hostname }}</h2>
                        <p class="text-sm text-gray-400 mt-1">VMID: {{ $container->vmid ?? 'Belum provisioning' }} • Node: {{ $container->node ?? '—' }}</p>
                    </div>
                    <x-ui.badge id="status-badge" :color="$container->status->color()">{{ $container->status->label() }}</x-ui.badge>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-gray-800 pt-4">
                    <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ $container->cores }}</p><p class="text-xs text-gray-500">vCPU</p></div>
                    <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ round($container->memory_mb/1024,1) }} GB</p><p class="text-xs text-gray-500">RAM</p></div>
                    <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ $container->disk_gb }} GB</p><p class="text-xs text-gray-500">Disk</p></div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-gray-800 pt-4 text-sm">
                    <div><p class="text-gray-500">IP Address</p><p class="font-mono text-gray-200">{{ $container->ip_address ?? '—' }}</p></div>
                    <div><p class="text-gray-500">OS Template</p><p class="text-gray-200">{{ $container->os_template ?? '—' }}</p></div>
                    <div><p class="text-gray-500">Dibuat</p><p class="text-gray-200">{{ $container->created_at->format('d M Y H:i') }}</p></div>
                    <div><p class="text-gray-500">Berakhir</p><p class="text-gray-200">{{ $container->expires_at ? $container->expires_at->format('d M Y') : '—' }}</p></div>
                </div>

                @if($container->notes)
                <div class="border-t border-gray-800 pt-3">
                    <p class="text-xs text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-300">{{ $container->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Port forwarding --}}
            @if($container->portForwarding && $container->portForwarding->count())
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 font-semibold text-gray-100">Port Forwarding</div>
                <table class="table">
                    <thead><tr><th>Protokol</th><th>Host Port</th><th>Container Port</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @foreach($container->portForwarding as $pf)
                        <tr>
                            <td class="uppercase text-xs text-gray-400">{{ $pf->protocol }}</td>
                            <td class="font-mono text-gray-200">{{ $pf->host_port }}</td>
                            <td class="font-mono text-gray-200">{{ $pf->container_port }}</td>
                            <td class="text-xs text-gray-400">{{ $pf->description ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Actions sidebar --}}
        <div class="space-y-4">
            <div class="card card-body">
                <h3 class="font-semibold text-gray-100 mb-2">Pemilik</h3>
                <a href="{{ route('admin.users.show', $container->user_id) }}" class="flex items-center gap-3 hover:opacity-80">
                    <div class="h-10 w-10 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold">{{ strtoupper(substr($container->user->name??'?',0,1)) }}</div>
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $container->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $container->user->email ?? '' }}</p>
                    </div>
                </a>
            </div>

            @if($container->vmid && ! $container->isProvisioning())
            <div class="card card-body space-y-2">
                <h3 class="font-semibold text-gray-100 mb-3">Aksi Proxmox</h3>

                {{-- Console --}}
                <a id="btn-console"
                   href="{{ route('admin.containers.console', $container) }}"
                   target="_blank"
                   class="btn-primary btn-sm w-full block text-center {{ $container->status->value !== 'running' ? 'opacity-50 pointer-events-none' : '' }}">
                    ⌨ Buka Console
                </a>

                <form method="POST" action="{{ route('admin.containers.action', $container) }}">
                    @csrf
                    <input type="hidden" name="action" value="start">
                    <button id="btn-start" type="submit" class="btn-primary btn-sm w-full"
                        {{ $container->status->value === 'running' ? 'disabled' : '' }}>▶ Start</button>
                </form>
                <form method="POST" action="{{ route('admin.containers.action', $container) }}">
                    @csrf
                    <input type="hidden" name="action" value="stop">
                    <button id="btn-stop" type="submit" class="btn-secondary btn-sm w-full"
                        {{ $container->status->value !== 'running' ? 'disabled' : '' }}>■ Stop</button>
                </form>
                <form method="POST" action="{{ route('admin.containers.action', $container) }}">
                    @csrf
                    <input type="hidden" name="action" value="shutdown">
                    <button id="btn-shutdown" type="submit" class="btn-ghost btn-sm w-full"
                        {{ $container->status->value !== 'running' ? 'disabled' : '' }}>⏻ Shutdown</button>
                </form>
                <form method="POST" action="{{ route('admin.containers.action', $container) }}">
                    @csrf
                    <input type="hidden" name="action" value="restart">
                    <button id="btn-restart" type="submit" class="btn-ghost btn-sm w-full"
                        {{ $container->status->value !== 'running' ? 'disabled' : '' }}>↺ Restart</button>
                </form>
            </div>
            @endif

            <div class="card card-body">
                <form method="POST" action="{{ route('admin.containers.destroy', $container) }}" data-confirm-form="Hapus container {{ $container->hostname }}? Tindakan ini tidak dapat dibatalkan.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full">Hapus Container</button>
                </form>
            </div>

            <a href="{{ route('admin.containers.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

    {{-- Realtime status polling --}}
    @if($container->vmid && ! $container->isProvisioning())
    <script>
        (function () {
            const statusUrl = '{{ route('admin.containers.status', $container) }}';
            const badgeEl    = document.getElementById('status-badge');
            const btnStart   = document.getElementById('btn-start');
            const btnStop    = document.getElementById('btn-stop');
            const btnShutdown= document.getElementById('btn-shutdown');
            const btnRestart = document.getElementById('btn-restart');
            const btnConsole = document.getElementById('btn-console');

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

                [btnStop, btnShutdown, btnRestart].forEach(btn => { if (btn) btn.disabled = ! isRunning; });
                if (btnStart)   btnStart.disabled = isRunning;
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
                    applyStatus(data);
                } catch (_) { }
            }

            setInterval(poll, 8000);
        })();
    </script>
    @endif

</x-layouts.admin>
