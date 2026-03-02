<x-layouts.portal title="Profil Saya">

    <div class="max-w-2xl space-y-6">
        {{-- Update profile info --}}
        <div class="card card-body space-y-5">
            <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Informasi Profil</h2>
            <form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <x-form.label for="name" required>Nama</x-form.label>
                    <x-form.input id="name" name="name" :value="old('name', $user->name)" autocomplete="name" required/>
                    <x-form.error field="name"/>
                </div>

                <div>
                    <x-form.label for="email" required>Email</x-form.label>
                    <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" autocomplete="email" required/>
                    <x-form.error field="email"/>
                    @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <p class="text-sm text-yellow-400 mt-1">Email belum diverifikasi.
                        <button form="send-verification" class="underline hover:text-yellow-300">Kirim ulang verifikasi.</button>
                    </p>
                    @if(session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm text-green-400">Link verifikasi telah dikirim.</p>
                    @endif
                    @endif
                </div>

                <button type="submit" class="btn-primary">Simpan</button>
            </form>

            <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">@csrf</form>
        </div>

        {{-- Update password --}}
        <div class="card card-body space-y-5">
            <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Ubah Password</h2>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <x-form.label for="current_password" required>Password Saat Ini</x-form.label>
                    <x-form.input id="current_password" name="current_password" type="password" autocomplete="current-password"/>
                    <x-form.error field="current_password"/>
                </div>

                <div>
                    <x-form.label for="password" required>Password Baru</x-form.label>
                    <x-form.input id="password" name="password" type="password" autocomplete="new-password"/>
                    <x-form.error field="password"/>
                </div>

                <div>
                    <x-form.label for="password_confirmation" required>Konfirmasi Password Baru</x-form.label>
                    <x-form.input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"/>
                    <x-form.error field="password_confirmation"/>
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>

        {{-- Delete account --}}
        <div class="card card-body border border-red-900/50">
            <h2 class="font-semibold text-red-400 border-b border-gray-800 pb-3 mb-4">Hapus Akun</h2>
            <p class="text-sm text-gray-400 mb-4">Setelah akun dihapus, semua data akan dihapus permanen. Pastikan kamu sudah mencadangkan semua data penting.</p>
            <form method="POST" action="{{ route('portal.profile.destroy') }}" data-confirm-form="Hapus akun? Tindakan ini tidak dapat dibatalkan.">
                @csrf @method('DELETE')
                <div class="mb-4">
                    <x-form.label for="delete_password" required>Konfirmasi dengan Password</x-form.label>
                    <x-form.input id="delete_password" name="password" type="password"/>
                    <x-form.error field="password"/>
                </div>
                <button type="submit" class="btn-danger btn-sm">Hapus Akun Saya</button>
            </form>
        </div>
    </div>

</x-layouts.portal>
