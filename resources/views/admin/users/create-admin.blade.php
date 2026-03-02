<x-layouts.admin title="Tambah Admin Baru">

    <div class="max-w-lg mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-100">Tambah Admin Baru</h1>
            <p class="text-sm text-gray-400 mt-1">Buat akun dengan role administrator untuk mengelola panel admin.</p>
        </div>

        <div class="card p-6">
            <form method="POST" action="{{ route('admin.users.store-admin') }}" class="space-y-5">
                @csrf

                <div>
                    <x-form.label for="name" required>Nama Lengkap</x-form.label>
                    <x-form.input id="name" name="name" type="text" :value="old('name')" placeholder="Admin Name" required autofocus />
                    <x-form.error field="name" />
                </div>

                <div>
                    <x-form.label for="email" required>Email</x-form.label>
                    <x-form.input id="email" name="email" type="email" :value="old('email')" placeholder="admin@example.com" required />
                    <x-form.error field="email" />
                </div>

                <div>
                    <x-form.label for="password" required>Password</x-form.label>
                    <x-form.input id="password" name="password" type="password" placeholder="••••••••" required />
                    <x-form.error field="password" />
                </div>

                <div>
                    <x-form.label for="password_confirmation" required>Konfirmasi Password</x-form.label>
                    <x-form.input id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••" required />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Tambah Admin</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>
