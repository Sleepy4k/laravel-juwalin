<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Console — {{ $container->hostname }}</title>
    <meta name="robots" content="noindex,nofollow">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.css">

    @vite(['resources/css/app.css'])

    <style>
        #terminal { width: 100%; height: 100%; }
        .xterm { padding: 8px; }
        .xterm-viewport { overflow-y: hidden !important; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden bg-gray-950 text-gray-100">

    {{-- Topbar --}}
    <header class="flex h-12 shrink-0 items-center justify-between border-b border-gray-800 bg-gray-900 px-4 gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('portal.containers.show', $container) }}"
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
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span id="conn-label" class="hidden sm:inline text-xs text-gray-500 mr-1">Menghubungkan…</span>
            <button onclick="toggleFullscreen()" class="btn-secondary btn-sm text-xs py-1 px-3">&#x26F6; Fullscreen</button>
        </div>
    </header>

    {{-- Terminal --}}
    <main id="terminal-container" class="relative flex-1 overflow-hidden bg-black">
        <div id="terminal"></div>

        <div id="status-overlay"
             class="absolute inset-0 flex flex-col items-center justify-center gap-4 pointer-events-none">
            <div class="h-10 w-10 rounded-full border-2 border-gray-800 border-t-brand-500 animate-spin"></div>
            <p class="text-sm text-gray-400">Menghubungkan ke console…</p>
        </div>

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
                <p class="text-xs text-gray-500">Pastikan container sedang berjalan. Refresh halaman untuk membuat sesi baru.</p>
            </div>
            <div class="flex gap-3 mt-1">
                <button onclick="location.reload()" class="btn-primary btn-sm text-xs">&#8635; Coba Lagi</button>
                <a href="{{ route('portal.containers.show', $container) }}" class="btn-secondary btn-sm text-xs">&#8592; Detail Container</a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.js"></script>
    <script>
        const statusDot     = document.getElementById('status-dot');
        const connLabel     = document.getElementById('conn-label');
        const overlay       = document.getElementById('status-overlay');
        const errOverlay    = document.getElementById('error-overlay');
        const errMsg        = document.getElementById('error-message');
        const termEl        = document.getElementById('terminal');
        const termContainer = document.getElementById('terminal-container');

        const term = new Terminal({
            cursorBlink: true,
            fontSize:    14,
            fontFamily:  'Menlo, Monaco, "Courier New", monospace',
            theme: { background: '#000000', foreground: '#f0f0f0', cursor: '#ffffff' },
            scrollback:  5000,
            convertEol:  true,
        });

        const fitAddon = new FitAddon.FitAddon();
        term.loadAddon(fitAddon);
        term.open(termEl);
        fitAddon.fit();

        function setConnected() {
            overlay.classList.add('hidden');
            statusDot.className   = 'h-2 w-2 shrink-0 rounded-full bg-green-400 ring-2 ring-green-400/20';
            connLabel.textContent = 'Terhubung';
            term.focus();
        }

        function showError(msg) {
            overlay.classList.add('hidden');
            errMsg.textContent = msg;
            errOverlay.classList.remove('hidden');
            errOverlay.style.display = 'flex';
            statusDot.className   = 'h-2 w-2 shrink-0 rounded-full bg-red-400 ring-2 ring-red-400/20';
            connLabel.textContent = 'Terputus';
        }

        let ws;

        function sendResize(cols, rows) {
            cols = cols ?? term.cols;
            rows = rows ?? term.rows;
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send('\x01' + cols + ':' + rows + '\n');
            }
        }

        async function connect() {
            connLabel.classList.remove('hidden');
            let wsUrl, ticket;
            try {
                const resp = await fetch(@json(route('portal.containers.term-url', $container->id)), {
                    headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' },
                });
                if (! resp.ok) {
                    const body = await resp.json().catch(() => ({}));
                    showError(body.message ?? 'Gagal mendapatkan sesi console (HTTP ' + resp.status + ').');
                    return;
                }
                ({ wsUrl, ticket } = await resp.json());
                console.log('[Term] wsUrl:', wsUrl);
            } catch (e) {
                showError('Tidak dapat terhubung ke server: ' + e.message);
                return;
            }

            ws = new WebSocket(wsUrl);
            ws.binaryType = 'arraybuffer';

            ws.onopen = () => {
                ws.send(ticket + '\n');
                sendResize();
                setConnected();
            };

            ws.onmessage = (e) => {
                term.write(e.data instanceof ArrayBuffer ? new Uint8Array(e.data) : e.data);
            };

            ws.onerror = () => {
                showError('Koneksi WebSocket gagal. Pastikan container sedang berjalan.');
            };

            ws.onclose = (e) => {
                console.log('[Term] close:', { code: e.code, clean: e.wasClean, reason: e.reason });
                showError(e.wasClean
                    ? 'Sesi console berakhir.'
                    : 'Koneksi terputus (kode ' + e.code + '). Coba refresh halaman.');
            };

            term.onData((data) => {
                if (ws.readyState === WebSocket.OPEN) ws.send(data);
            });

            term.onResize(({ cols, rows }) => sendResize(cols, rows));
        }

        const resizeObserver = new ResizeObserver(() => fitAddon.fit());
        resizeObserver.observe(termContainer);

        window.toggleFullscreen = () => {
            document.fullscreenElement
                ? document.exitFullscreen()
                : document.documentElement.requestFullscreen();
        };

        connect();
    </script>
</body>
</html>