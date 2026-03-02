<x-layouts.portal title="Request Port Forwarding">
<div class="mx-auto max-w-xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.port-forwarding.index') }}" class="text-sm text-gray-400 hover:text-white">← Back</a>
        <h1 class="text-2xl font-bold text-white">Request Port Forwarding</h1>
    </div>

    <form method="POST" action="{{ route('portal.port-forwarding.store') }}"
          class="rounded-xl border border-gray-800 bg-gray-900 p-6 space-y-5">
        @csrf

        {{-- Container --}}
        <div class="space-y-1.5">
            <label for="container_id" class="text-sm font-medium text-gray-300">Container</label>
            <select name="container_id" id="container_id"
                    class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                @forelse ($containers as $ct)
                    <option value="{{ $ct->id }}" @selected(old('container_id') == $ct->id)>
                        {{ $ct->hostname }} (VMID {{ $ct->vmid }})
                    </option>
                @empty
                    <option disabled>No running containers</option>
                @endforelse
            </select>
            @error('container_id')
                <p class="text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Protocol --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-300">Protocol</label>
            <div class="flex gap-4">
                @foreach (['tcp', 'udp'] as $proto)
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-300">
                        <input type="radio" name="protocol" value="{{ $proto }}"
                               @checked(old('protocol', 'tcp') === $proto)
                               class="accent-indigo-500">
                        {{ strtoupper($proto) }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Ports --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="source_port" class="text-sm font-medium text-gray-300">
                    Public Port (gateway)
                </label>
                <input type="number" name="source_port" id="source_port"
                       min="1024" max="65535"
                       value="{{ old('source_port') }}"
                       placeholder="e.g. 8080"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500 focus:outline-none">
                @error('source_port')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-1.5">
                <label for="destination_port" class="text-sm font-medium text-gray-300">
                    Container Port
                </label>
                <input type="number" name="destination_port" id="destination_port"
                       min="1" max="65535"
                       value="{{ old('destination_port') }}"
                       placeholder="e.g. 80"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500 focus:outline-none">
                @error('destination_port')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Reason --}}
        <div class="space-y-1.5">
            <label for="reason" class="text-sm font-medium text-gray-300">
                Reason <span class="text-gray-500">(required for approval)</span>
            </label>
            <textarea name="reason" id="reason" rows="3"
                      placeholder="Briefly explain why you need this port exposed..."
                      class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder:text-gray-600 focus:border-indigo-500 focus:outline-none">{{ old('reason') }}</textarea>
            @error('reason')
                <p class="text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition-colors hover:bg-indigo-500">
            Submit Request
        </button>
    </form>
</div>

</x-layouts.portal>
