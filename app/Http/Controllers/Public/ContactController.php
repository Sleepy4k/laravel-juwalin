<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Settings\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function send(Request $request, SiteSettings $settings): RedirectResponse
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // TODO: mail to $settings->contact_email

        return redirect()->route('contact')
            ->with('success', 'Pesan Anda berhasil dikirim. Kami akan menghubungi Anda segera.');
    }
}
