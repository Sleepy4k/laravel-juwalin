<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService) {}

    public function index()
    {
        $settings = $this->settingsService->all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name'                => ['required', 'string', 'max:100'],
            'app_tagline'             => ['nullable', 'string', 'max:200'],
            'app_description'         => ['nullable', 'string', 'max:500'],
            'app_logo'                => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'app_favicon'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico,svg', 'max:512'],
            'contact_email'           => ['required', 'email'],
            'contact_phone'           => ['nullable', 'string', 'max:20'],
            'contact_address'         => ['nullable', 'string', 'max:300'],
            'social_instagram'        => ['nullable', 'url'],
            'social_twitter'          => ['nullable', 'url'],
            'social_facebook'         => ['nullable', 'url'],
            'social_youtube'          => ['nullable', 'url'],
            'meta_title'              => ['nullable', 'string', 'max:100'],
            'meta_description'        => ['nullable', 'string', 'max:300'],
            'meta_keywords'           => ['nullable', 'string', 'max:200'],
            'maintenance_mode'        => ['boolean'],
            'maintenance_message'     => ['nullable', 'string', 'max:500'],
            'payment_gateway'         => ['nullable', 'string', 'in:manual,pakasir'],
            'payment_pakasir_project' => ['nullable', 'string', 'max:100'],
            'payment_pakasir_api_key' => ['nullable', 'string', 'max:255'],
            'payment_sandbox'         => ['boolean'],
            'currency'                => ['nullable', 'string', 'max:10'],
        ]);

        $data['maintenance_mode'] = $request->boolean('maintenance_mode');
        $data['payment_sandbox'] = $request->boolean('payment_sandbox');

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $currentSettings = $this->settingsService->all();
            if (!empty($currentSettings['app_logo'])) {
                Storage::disk('public')->delete($currentSettings['app_logo']);
            }
            $data['app_logo'] = $request->file('app_logo')->store('site', 'public');
        } else {
            unset($data['app_logo']);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $currentSettings = $this->settingsService->all();
            if (!empty($currentSettings['app_favicon'])) {
                Storage::disk('public')->delete($currentSettings['app_favicon']);
            }
            $data['app_favicon'] = $request->file('app_favicon')->store('site', 'public');
        } else {
            unset($data['app_favicon']);
        }

        $this->settingsService->setMany($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
