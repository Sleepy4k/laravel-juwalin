<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortForwardingRequest as PortForwardingFormRequest;
use App\Models\PortForwardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortForwardingController extends Controller
{
    /**
     * GET /portal/port-forwarding.
     */
    public function index(Request $request): View
    {
        $requests = PortForwardingRequest::query()
            ->where('user_id', $request->user()->id)
            ->with('container')
            ->latest()
            ->paginate(10);

        return view('portal.port-forwarding.index', compact('requests'));
    }

    /**
     * GET /portal/port-forwarding/create.
     */
    public function create(Request $request): View
    {
        $containers = $request->user()
            ->containers()
            ->where('status', 'running')
            ->get();

        return view('portal.port-forwarding.create', compact('containers'));
    }

    /**
     * POST /portal/port-forwarding.
     */
    public function store(PortForwardingFormRequest $request): RedirectResponse
    {
        PortForwardingRequest::query()->create([
            'user_id'          => $request->user()->id,
            'container_id'     => $request->integer('container_id'),
            'protocol'         => $request->input('protocol'),
            'source_port'      => $request->integer('source_port'),
            'destination_port' => $request->integer('destination_port'),
            'reason'           => $request->input('reason'),
            'status'           => 'pending',
        ]);

        return redirect()->route('portal.port-forwarding.index')
            ->with('success', 'Port forwarding request submitted. Pending admin approval.');
    }
}
