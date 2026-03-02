<x-layouts.auth title="Masuk">
    <x-slot:footer>
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Daftar sekarang</a>
    </x-slot:footer>

    <h2 class="text-xl font-bold text-white mb-6 text-center">Masuk ke Akun Anda</h2>

    @if(session('status'))
        <x-ui.alert type="success" class="mb-4">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-form.label for="email" required>Email</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email')" placeholder="email@anda.com" required autofocus/>
            <x-form.error field="email"/>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-form.label for="password">Password</x-form.label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Lupa password?</a>
                @endif
            </div>
            <x-form.input id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password"/>
            <x-form.error field="password"/>
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" name="remember" type="checkbox"
                   class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-brand-500 focus:ring-brand-500/30">
            <label for="remember_me" class="text-sm text-gray-400">Ingat saya</label>
        </div>

        <button type="submit" class="btn-primary w-full">Masuk</button>
    </form>
</x-layouts.auth>

