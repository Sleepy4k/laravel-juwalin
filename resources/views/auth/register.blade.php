<x-layouts.auth title="Daftar">
    <x-slot:footer>
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Masuk</a>
    </x-slot:footer>

    <h2 class="text-xl font-bold text-white mb-6 text-center">Buat Akun Baru</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-form.label for="name" required>Nama Lengkap</x-form.label>
            <x-form.input id="name" name="name" :value="old('name')" placeholder="Nama Anda" required autofocus/>
            <x-form.error field="name"/>
        </div>

        <div>
            <x-form.label for="email" required>Email</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email')" placeholder="email@anda.com" required/>
            <x-form.error field="email"/>
        </div>

        <div>
            <x-form.label for="password" required>Password</x-form.label>
            <x-form.input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required autocomplete="new-password"/>
            <x-form.error field="password"/>
        </div>

        <div>
            <x-form.label for="password_confirmation" required>Konfirmasi Password</x-form.label>
            <x-form.input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" required/>
            <x-form.error field="password_confirmation"/>
        </div>

        <button type="submit" class="btn-primary w-full">Buat Akun</button>
    </form>
</x-layouts.auth>

