<x-layouts.auth title="Verifikasi Email">
    <div class="text-center mb-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-500/10 text-brand-400 mx-auto mb-4">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">Verifikasi Email Anda</h2>
        <p class="text-sm text-gray-400">Kami telah mengirimkan link verifikasi ke email Anda. Klik link tersebut untuk mengaktifkan akun.</p>
    </div>

    @if(session('status') === 'verification-link-sent')
        <x-ui.alert type="success" class="mb-4">Link verifikasi baru telah dikirimkan ke email Anda.</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
        @csrf
        <button type="submit" class="btn-primary w-full">Kirim Ulang Email Verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn-ghost btn-sm w-full text-gray-500">Keluar</button>
    </form>
</x-layouts.auth>

