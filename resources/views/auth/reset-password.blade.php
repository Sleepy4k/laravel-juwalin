<x-layouts.auth title="Reset Password">
    <h2 class="text-xl font-bold text-white mb-6 text-center">Buat Password Baru</h2>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-form.label for="email" required>Email</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email', $request->email)" required/>
            <x-form.error field="email"/>
        </div>

        <div>
            <x-form.label for="password" required>Password Baru</x-form.label>
            <x-form.input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required/>
            <x-form.error field="password"/>
        </div>

        <div>
            <x-form.label for="password_confirmation" required>Konfirmasi Password</x-form.label>
            <x-form.input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password baru" required/>
            <x-form.error field="password_confirmation"/>
        </div>

        <button type="submit" class="btn-primary w-full">Reset Password</button>
    </form>
</x-layouts.auth>

