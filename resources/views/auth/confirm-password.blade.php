<x-layouts.auth>
    <x-slot:title>Konfirmasi Password</x-slot:title>

    <p class="text-sm text-gray-400 mb-6">Area aman. Konfirmasi password sebelum melanjutkan.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-form.label for="password" required>Password</x-form.label>
            <x-form.input id="password" name="password" type="password" autocomplete="current-password" required/>
            <x-form.error field="password"/>
        </div>

        <button type="submit" class="btn-primary w-full">Konfirmasi</button>
    </form>
</x-layouts.auth>
