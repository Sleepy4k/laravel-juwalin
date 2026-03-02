<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Console [Admin] — {{ $container->hostname }}</title>
    <meta name="robots" content="noindex,nofollow">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="h-screen flex flex-col overflow-hidden bg-gray-950 text-gray-100">

    {{-- ── Topbar ── --}}
    <header class="flex h-12 shrink-0 items-center justify-between border-b border-gray-800 bg-gray-900 px-4 gap-4">

        {{-- Left: back + container identity --}}
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.containers.show', $container) }}"
               class="flex items-center gap-1.5 text-sm font-medium text-gray-400 hover:text-white transition-colors shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>

            <span class="text-gray-700 select-none">|</span>

            <div class="flex items-center gap-2 min-w-0">
                <span id="status-dot" class="h-2 w-2 shrink-0 rounded-full bg-yellow-400 ring-2 ring-yellow-400/20"></span>
                <span class="font-mono font-semibold text-sm text-gray-100 truncate">{{ $container->hostname }}</span>
                <span class="hidden sm:inline text-xs text-gray-500 shrink-0">
                    VMID&nbsp;{{ $container->vmid }}&nbsp;&bull;&nbsp;{{ $container->node }}
                </span>
                <span class="hidden sm:inline text-xs font-medium text-brand-400 bg-brand-500/10 px-1.5 py-0.5 rounded shrink-0">Admin</span>
            </div>
        </div>

        {{-- Right: status label + controls --}}
        <div class="flex items-center gap-2 shrink-0">
            <span id="conn-label" class="hidden sm:inline text-xs text-gray-500 mr-1">Menghubungkan…</span>
            <button onclick="sendCtrlAltDel()"
                    class="btn-secondary btn-sm text-xs py-1 px-3">
                Ctrl+Alt+Del
            </button>
            <button onclick="toggleFullscreen()"
                    class="btn-secondary btn-sm text-xs py-1 px-3">
                &#x26F6; Fullscreen
            </button>
        </div>
    </header>

    {{-- ── Terminal area ── --}}
    <main class="relative flex-1 overflow-hidden bg-black">
        <div id="screen" class="w-full h-full"></div>

        {{-- Connecting overlay --}}
        <div id="status-overlay"
             class="absolute inset-0 flex flex-col items-center justify-center gap-4 pointer-events-none">
            <div class="h-10 w-10 rounded-full border-2 border-gray-800 border-t-brand-500 animate-spin"></div>
            <p class="text-sm text-gray-400">Menghubungkan ke console…</p>
        </div>

        {{-- Error overlay --}}
        <div id="error-overlay"
             class="absolute inset-0 hidden flex-col items-center justify-center gap-4 bg-gray-950/90 px-6">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-500/10">
                <svg class="h-7 w-7 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div class="text-center space-y-1 max-w-sm">
                <p id="error-message" class="text-sm font-medium text-red-400">Koneksi ke console gagal.</p>
                <p class="text-xs text-gray-500">
                    Pastikan container sedang berjalan. Refresh halaman untuk membuat sesi baru.
                </p>
            </div>
            <div class="flex gap-3 mt-1">
                <button onclick="location.reload()" class="btn-primary btn-sm text-xs">&#8635; Coba Lagi</button>
                <a href="{{ route('admin.containers.show', $container) }}" class="btn-secondary btn-sm text-xs">&#8592; Detail Container</a>
            </div>
        </div>
    </main>

    <script type="module">
        import RFB from 'https://cdn.jsdelivr.net/npm/@novnc/novnc@1.4.0/core/rfb.js';

        const statusDot  = document.getElementById('status-dot');
        const connLabel  = document.getElementById('conn-label');
        const overlay    = document.getElementById('status-overlay');
        const errOverlay = document.getElementById('error-overlay');
        const errMsg     = document.getElementById('error-message');
        const screen     = document.getElementById('screen');

        let rfb;

        function setConnected() {
            overlay.classList.add('hidden');
            statusDot.className  = 'h-2 w-2 shrink-0 rounded-full bg-green-400 ring-2 ring-green-400/20';
            connLabel.textContent = 'Terhubung';
        }

        function showError(msg) {
            overlay.classList.add('hidden');
            errMsg.textContent = msg;
            errOverlay.classList.remove('hidden');
            errOverlay.style.display = 'flex';
            statusDot.className  = 'h-2 w-2 shrink-0 rounded-full bg-red-400 ring-2 ring-red-400/20';
            connLabel.textContent = 'Terputus';
        }

        async function connect() {
            connLabel.classList.remove('hidden');

            let wsUrl;
            try {
                const resp = await fetch(@json(route('admin.containers.vnc-url', $container)), {
                    headers: {
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json',
                    },
                });

                if (! resp.ok) {
                    const body = await resp.json().catch(() => ({}));
                    showError(body.message ?? 'Gagal mendapatkan sesi console (HTTP ' + resp.status + ').');
                    return;
                }

                ({ wsUrl } = await resp.json());
                console.log('[VNC] wsUrl:', wsUrl);
            } catch (e) {
                showError('Tidak dapat terhubung ke server: ' + e.message);
                return;
            }

            try {
                rfb = new RFB(screen, wsUrl);
                rfb.viewOnly      = false;
                rfb.scaleViewport = true;
                rfb.resizeSession = true;
                rfb.qualityLevel  = 6;

                rfb.addEventListener('connect', setConnected);
                rfb.addEventListener('disconnect', (e) => {
                    console.log('[VNC] disconnect:', e.detail);
                    const reason = e.detail?.clean
                        ? 'Sesi console berakhir (container mungkin dimatikan).'
                        : 'Koneksi terputus: ' + (e.detail?.reason || 'alasan tidak diketahui');
                    showError(reason);
                });
                rfb.addEventListener('credentialsrequired', () => rfb.sendCredentials({ password: '' }));
            } catch (err) {
                showError('Gagal menginisialisasi noVNC: ' + err.message);
            }
        }

        connect();

        window.sendCtrlAltDel  = () => rfb?.sendCtrlAltDel();
        window.toggleFullscreen = () => {
            document.fullscreenElement
                ? document.exitFullscreen()
                : document.documentElement.requestFullscreen();
        };
    </script>
</body>
</html>
