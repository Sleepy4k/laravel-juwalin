<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortForwardingRequest as PortForwardingFormRequest;
use App\Models\PortForwardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortForwardingController extends Controller
{
    /**
     * GET /portal/containers/{container}/ports.
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
     * POST /portal/containers/{container}/ports.
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

        return redirect()->route('portal.ports.index', $request->integer('container_id'))
            ->with('success', 'Port forwarding request submitted. Pending admin approval.');
    }

    /**
     * DELETE /portal/ports/{portForwardingRequest}.
     */
    public function destroy(Request $request, PortForwardingRequest $portForwardingRequest): RedirectResponse
    {
        abort_unless($portForwardingRequest->user_id === $request->user()->id, 403);

        $portForwardingRequest->delete();

        return back()->with('success', 'Port forwarding request dihapus.');
    }
}
