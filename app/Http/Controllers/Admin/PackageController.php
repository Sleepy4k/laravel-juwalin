<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('sort_order')->paginate(15);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'cores'         => ['required', 'integer', 'min:1'],
            'memory_mb'     => ['required', 'integer', 'min:128'],
            'disk_gb'       => ['required', 'integer', 'min:1'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_setup'   => ['nullable', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
            'is_featured'   => ['boolean'],
            'sort_order'    => ['integer', 'min:0'],
            'features'      => ['nullable', 'string'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['features'] = $this->parseFeatures($request->input('features', ''));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        Package::create($data);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function show(Package $package)
    {
        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'cores'         => ['required', 'integer', 'min:1'],
            'memory_mb'     => ['required', 'integer', 'min:128'],
            'disk_gb'       => ['required', 'integer', 'min:1'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_setup'   => ['nullable', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
            'is_featured'   => ['boolean'],
            'sort_order'    => ['integer', 'min:0'],
            'features'      => ['nullable', 'string'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['features'] = $this->parseFeatures($request->input('features', ''));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $package->update($data);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function parseFeatures(string $raw): array
    {
        return array_values(
            array_filter(
                array_map('trim', explode("\n", $raw)),
            ),
        );
    }
}
