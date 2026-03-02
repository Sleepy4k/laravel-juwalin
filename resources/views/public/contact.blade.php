<x-layouts.public title="Kontak">
    <section class="py-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-white mb-3">Hubungi Kami</h1>
                <p class="text-gray-400">Ada pertanyaan? Kami siap membantu.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Info --}}
                <div class="space-y-6">
                    @if($siteSettings->contact_email)
                    <div class="card card-body">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Email</p>
                        <a href="mailto:{{ $siteSettings->contact_email }}" class="text-brand-400 hover:text-brand-300 transition-colors text-sm">
                            {{ $siteSettings->contact_email }}
                        </a>
                    </div>
                    @endif
                    @if($siteSettings->contact_phone)
                    <div class="card card-body">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">WhatsApp / Telepon</p>
                        <a href="tel:{{ $siteSettings->contact_phone }}" class="text-brand-400 hover:text-brand-300 transition-colors text-sm">
                            {{ $siteSettings->contact_phone }}
                        </a>
                    </div>
                    @endif
                    @if($siteSettings->contact_address)
                    <div class="card card-body">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Alamat</p>
                        <p class="text-gray-300 text-sm">{{ $siteSettings->contact_address }}</p>
                    </div>
                    @endif
                </div>

                {{-- Form --}}
                <div class="lg:col-span-2 card card-body">
                    <h2 class="text-lg font-semibold text-white mb-6">Kirim Pesan</h2>

                    @if(session('success'))
                        <x-ui.alert type="success" class="mb-6">{{ session('success') }}</x-ui.alert>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <x-form.label for="name" required>Nama</x-form.label>
                                <x-form.input id="name" name="name" :value="old('name')" placeholder="Nama lengkap" required/>
                                <x-form.error field="name"/>
                            </div>
                            <div>
                                <x-form.label for="email" required>Email</x-form.label>
                                <x-form.input id="email" name="email" type="email" :value="old('email')" placeholder="email@anda.com" required/>
                                <x-form.error field="email"/>
                            </div>
                        </div>

                        <div>
                            <x-form.label for="subject" required>Subjek</x-form.label>
                            <x-form.input id="subject" name="subject" :value="old('subject')" placeholder="Pertanyaan tentang..." required/>
                            <x-form.error field="subject"/>
                        </div>

                        <div>
                            <x-form.label for="message" required>Pesan</x-form.label>
                            <x-form.textarea id="message" name="message" :rows="6" placeholder="Tulis pesan Anda di sini...">{{ old('message') }}</x-form.textarea>
                            <x-form.error field="message"/>
                        </div>

                        <button type="submit" class="btn-primary w-full">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
