<x-layouts.admin title="Pengaturan Sistem">
    @push('head')
        @vite('resources/js/pages/admin/settings.js')
    @endpush

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Site Info --}}
            <div class="card card-body space-y-5" id="section-site">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Informasi Situs</h2>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="app_name" required>Nama Aplikasi</x-form.label>
                        <x-form.input id="app_name" name="app_name" :value="old('app_name', $settings->app_name)" data-app-name-input/>
                        <x-form.error field="app_name"/>
                        <p class="text-xs text-gray-500 mt-1">Preview: <span data-app-name-preview class="text-brand-400">{{ $settings->app_name }}</span></p>
                    </div>
                    <div>
                        <x-form.label for="app_tagline">Tagline</x-form.label>
                        <x-form.input id="app_tagline" name="app_tagline" :value="old('app_tagline', $settings->app_tagline)"/>
                        <x-form.error field="app_tagline"/>
                    </div>
                </div>
                <div>
                    <x-form.label for="app_description">Deskripsi Singkat</x-form.label>
                    <x-form.textarea id="app_description" name="app_description" :rows="2">{{ old('app_description', $settings->app_description) }}</x-form.textarea>
                    <x-form.error field="app_description"/>
                </div>

                {{-- Logo & Favicon --}}
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="app_logo">Logo Situs</x-form.label>
                        @if($settings->app_logo)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ Storage::url($settings->app_logo) }}" alt="Logo" class="h-10 rounded border border-gray-700">
                                <span class="text-xs text-gray-500">Logo saat ini</span>
                            </div>
                        @endif
                        <input type="file" id="app_logo" name="app_logo" accept="image/*"
                               class="block w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600"/>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG atau WebP. Maks 2MB.</p>
                        <x-form.error field="app_logo"/>
                    </div>
                    <div>
                        <x-form.label for="app_favicon">Favicon</x-form.label>
                        @if($settings->app_favicon)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ Storage::url($settings->app_favicon) }}" alt="Favicon" class="h-8 w-8 rounded border border-gray-700">
                                <span class="text-xs text-gray-500">Favicon saat ini</span>
                            </div>
                        @endif
                        <input type="file" id="app_favicon" name="app_favicon" accept="image/*,.ico"
                               class="block w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600"/>
                        <p class="text-xs text-gray-500 mt-1">PNG, ICO, atau SVG. Maks 512KB.</p>
                        <x-form.error field="app_favicon"/>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="card card-body space-y-5">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Kontak</h2>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="contact_email">Email Kontak</x-form.label>
                        <x-form.input id="contact_email" name="contact_email" type="email" :value="old('contact_email', $settings->contact_email)"/>
                        <x-form.error field="contact_email"/>
                    </div>
                    <div>
                        <x-form.label for="contact_phone">Nomor Telepon</x-form.label>
                        <x-form.input id="contact_phone" name="contact_phone" :value="old('contact_phone', $settings->contact_phone)"/>
                        <x-form.error field="contact_phone"/>
                    </div>
                </div>
                <div>
                    <x-form.label for="contact_address">Alamat</x-form.label>
                    <x-form.textarea id="contact_address" name="contact_address" :rows="2">{{ old('contact_address', $settings->contact_address) }}</x-form.textarea>
                    <x-form.error field="contact_address"/>
                </div>
            </div>

            {{-- Social --}}
            <div class="card card-body space-y-5">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Media Sosial</h2>
                <div class="grid grid-cols-2 gap-5">
                    @foreach(['instagram','twitter','facebook','youtube'] as $social)
                    <div>
                        <x-form.label for="social_{{ $social }}">{{ ucfirst($social) }}</x-form.label>
                        <x-form.input id="social_{{ $social }}" name="social_{{ $social }}" :value="old('social_'.$social, $settings->{'social_'.$social})"/>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Payment --}}
            <div class="card card-body space-y-5">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Payment Gateway</h2>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="payment_gateway">Gateway</x-form.label>
                        <x-form.select id="payment_gateway" name="payment_gateway">
                            <option value="manual" {{ old('payment_gateway', $settings->payment_gateway) === 'manual' ? 'selected' : '' }}>Manual Transfer</option>
                            <option value="pakasir" {{ old('payment_gateway', $settings->payment_gateway) === 'pakasir' ? 'selected' : '' }}>Pakasir (QRIS / VA)</option>
                        </x-form.select>
                    </div>
                    <div>
                        <x-form.label for="currency">Mata Uang</x-form.label>
                        <x-form.input id="currency" name="currency" :value="old('currency', $settings->currency ?? 'IDR')"/>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <x-form.label for="payment_pakasir_project">Pakasir Project Slug</x-form.label>
                        <x-form.input id="payment_pakasir_project" name="payment_pakasir_project" :value="old('payment_pakasir_project', $settings->payment_pakasir_project)" placeholder="your-project-slug"/>
                        <p class="text-xs text-gray-500 mt-1">Ditemukan di halaman detail proyek Pakasir.</p>
                    </div>
                    <div>
                        <x-form.label for="payment_pakasir_api_key">Pakasir API Key</x-form.label>
                        <x-form.input id="payment_pakasir_api_key" name="payment_pakasir_api_key" type="password" :value="old('payment_pakasir_api_key', $settings->payment_pakasir_api_key)" autocomplete="off"/>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="payment_sandbox" value="1" {{ old('payment_sandbox', $settings->payment_sandbox ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-brand-500">
                    <span class="text-sm text-gray-300">Mode Sandbox (Testing)</span>
                </label>
            </div>

            {{-- Maintenance --}}
            <div class="card card-body space-y-4">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Mode Maintenance</h2>
                <label class="flex items-center gap-3 cursor-pointer" data-maintenance-toggle>
                    <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-gray-600 bg-gray-800 text-brand-500" id="maintenance_mode">
                    <span class="text-sm text-gray-300">Aktifkan Mode Maintenance</span>
                </label>
                <div id="maintenance-warning" class="{{ $settings->maintenance_mode ? '' : 'hidden' }} p-3 bg-yellow-900/30 border border-yellow-700 rounded-lg">
                    <p class="text-sm text-yellow-300">⚠️ Website tidak dapat diakses user saat mode maintenance aktif.</p>
                </div>
                <div>
                    <x-form.label for="maintenance_message">Pesan Maintenance</x-form.label>
                    <x-form.textarea id="maintenance_message" name="maintenance_message" :rows="2">{{ old('maintenance_message', $settings->maintenance_message ?? 'Sistem sedang dalam pemeliharaan. Mohon tunggu...') }}</x-form.textarea>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card card-body space-y-5">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">SEO Meta</h2>
                <div>
                    <x-form.label for="meta_title">Meta Title</x-form.label>
                    <x-form.input id="meta_title" name="meta_title" :value="old('meta_title', $settings->meta_title)"/>
                </div>
                <div>
                    <x-form.label for="meta_description">Meta Description</x-form.label>
                    <x-form.textarea id="meta_description" name="meta_description" :rows="2">{{ old('meta_description', $settings->meta_description) }}</x-form.textarea>
                </div>
                <div>
                    <x-form.label for="meta_keywords">Keywords (pisahkan dengan koma)</x-form.label>
                    <x-form.input id="meta_keywords" name="meta_keywords" :value="old('meta_keywords', $settings->meta_keywords)"/>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>

</x-layouts.admin>

