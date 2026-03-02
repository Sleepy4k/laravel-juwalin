<x-layouts.admin title="Edit Paket">

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="card card-body space-y-5">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Informasi Paket</h2>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="name" required>Nama Paket</x-form.label>
                        <x-form.input id="name" name="name" :value="old('name', $package->name)" placeholder="Starter"/>
                        <x-form.error field="name"/>
                    </div>
                    <div>
                        <x-form.label for="sort_order">Urutan</x-form.label>
                        <x-form.input id="sort_order" name="sort_order" type="number" :value="old('sort_order', $package->sort_order)" min="0"/>
                        <x-form.error field="sort_order"/>
                    </div>
                </div>

                <div>
                    <x-form.label for="description">Deskripsi</x-form.label>
                    <x-form.textarea id="description" name="description" :rows="2">{{ old('description', $package->description) }}</x-form.textarea>
                    <x-form.error field="description"/>
                </div>

                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3 pt-2">Spesifikasi</h2>

                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <x-form.label for="cores" required>vCPU Core</x-form.label>
                        <x-form.input id="cores" name="cores" type="number" :value="old('cores', $package->cores)" min="1"/>
                        <x-form.error field="cores"/>
                    </div>
                    <div>
                        <x-form.label for="memory_mb" required>RAM (MB)</x-form.label>
                        <x-form.input id="memory_mb" name="memory_mb" type="number" :value="old('memory_mb', $package->memory_mb)" min="128"/>
                        <x-form.error field="memory_mb"/>
                    </div>
                    <div>
                        <x-form.label for="disk_gb" required>Disk (GB)</x-form.label>
                        <x-form.input id="disk_gb" name="disk_gb" type="number" :value="old('disk_gb', $package->disk_gb)" min="1"/>
                        <x-form.error field="disk_gb"/>
                    </div>
                </div>

                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3 pt-2">Harga</h2>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="price_monthly" required>Harga/Bulan (IDR)</x-form.label>
                        <x-form.input id="price_monthly" name="price_monthly" type="number" :value="old('price_monthly', $package->price_monthly)" min="0"/>
                        <x-form.error field="price_monthly"/>
                    </div>
                    <div>
                        <x-form.label for="price_setup">Biaya Setup (IDR)</x-form.label>
                        <x-form.input id="price_setup" name="price_setup" type="number" :value="old('price_setup', $package->price_setup ?? 0)" min="0"/>
                        <x-form.error field="price_setup"/>
                    </div>
                </div>

                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3 pt-2">Fitur & Opsi</h2>

                <div>
                    <x-form.label for="features">Daftar Fitur (satu per baris)</x-form.label>
                    <x-form.textarea id="features" name="features" :rows="6">{{ old('features', is_array($package->features) ? implode("\n", $package->features) : $package->features) }}</x-form.textarea>
                    <x-form.error field="features"/>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-brand-500">
                        <span class="text-sm text-gray-300">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-brand-500">
                        <span class="text-sm text-gray-300">Tampilkan sebagai Populer</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Update Paket</button>
                <a href="{{ route('admin.packages.index') }}" class="btn-secondary">Batal</a>
                <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="ml-auto" data-confirm-form="Hapus paket {{ $package->name }}?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">Hapus Paket</button>
                </form>
            </div>
        </form>
    </div>

</x-layouts.admin>
