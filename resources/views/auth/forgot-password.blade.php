<x-layouts.auth title="Lupa Password">
    <x-slot:footer>
        Ingat password?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Masuk</a>
    </x-slot:footer>

    <h2 class="text-xl font-bold text-white mb-2 text-center">Reset Password</h2>
    <p class="text-sm text-gray-400 text-center mb-6">Masukkan email Anda dan kami akan mengirim link reset password.</p>

    @if(session('status'))
        <x-ui.alert type="success" class="mb-4">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-form.label for="email" required>Email</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email')" placeholder="email@anda.com" required autofocus/>
            <x-form.error field="email"/>
        </div>

        <button type="submit" class="btn-primary w-full">Kirim Link Reset</button>
    </form>
</x-layouts.auth>

