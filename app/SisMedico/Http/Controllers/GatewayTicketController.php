<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\GatewayTicketService;
use Sis_medico\GatewayTicket;
use Sis_medico\Services\GatewayAttachmentService;

class GatewayTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        abort_if(Auth::user()->id_tipo_usuario == 2, 403);
        $externalId = $request->old('external_id');
        if (!$externalId || !$request->session()->has('gateway_drafts.' . $externalId)) {
            $externalId = 'SUC-' . bin2hex(random_bytes(16));
            $request->session()->put('gateway_drafts.' . $externalId, (string) Auth::id());
        }
        if (!$request->session()->has('gateway_origins.' . $externalId)) {
            $request->session()->put('gateway_origins.' . $externalId, $request->root());
        }
        return view('ticket_soporte_tecnico.gateway', [
            'externalId' => $externalId,
            'originUrl' => $request->session()->get('gateway_origins.' . $externalId),
            'configured' => (bool) config('gateway.url') && (bool) config('gateway.token'),
        ]);
    }

    public function store(Request $request, GatewayTicketService $gateway)
    {
        abort_if(Auth::user()->id_tipo_usuario == 2, 403);
        $this->validate($request, [
            'external_id' => 'required|string|max:120|regex:/^SUC-[a-f0-9]{32}$/',
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:20000',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachments' => 'array|max:5',
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);
        $externalId = $request->input('external_id');
        abort_unless($request->session()->get('gateway_drafts.' . $externalId) === (string) Auth::id(), 403);
        $payload = [
            'external_id' => $externalId,
            'title' => trim($request->input('title')),
            'description' => trim($request->input('description')),
            'priority' => $request->input('priority'),
        ];
        if ($payload['title'] === '' || $payload['description'] === '') {
            return redirect()->route('gateway_tickets.create')->withInput()->withErrors(['gateway' => 'Ingrese el título y la descripción del ticket.']);
        }
        // Conservar el origen durante los reintentos de la misma referencia.
        if (!$request->session()->has('gateway_origins.' . $externalId)) {
            $request->session()->put('gateway_origins.' . $externalId, $request->root());
        }
        $originUrl = $request->session()->get('gateway_origins.' . $externalId);
        try {
            $attachments = (new GatewayAttachmentService())->prepare($externalId, (string) Auth::id(), $request->file('attachments') ?: []);
            if (count($attachments)) {
                $payload['description'] .= "\n\nEvidencia adjunta (enlaces privados de descarga):";
                foreach ($attachments as $attachment) {
                    $payload['description'] .= "\n" . $attachment->name . ': ' . $attachment->download_url;
                }
            }
        } catch (\RuntimeException $exception) {
            return redirect()->route('gateway_tickets.create')->withInput()->withErrors(['attachments' => $exception->getMessage()]);
        }
        // Fijar la identidad del solicitante para conservar el contenido en los reintentos.
        $creatorKey = 'gateway_creators.' . $externalId;
        if (!$request->session()->has($creatorKey)) {
            $user = Auth::user();
            $name = trim(implode(' ', array_filter([
                $user->nombre1, $user->nombre2, $user->apellido1, $user->apellido2,
            ], function ($part) { return trim((string) $part) !== ''; })));
            $request->session()->put($creatorKey, [
                'id' => (string) Auth::id(),
                'name' => preg_replace('/\s+/u', ' ', $name),
            ]);
        }
        $creator = $request->session()->get($creatorKey);
        $payload['description'] .= "\n\nCreado por: " . $creator['name'] . "\nCédula: " . $creator['id'];
        $originText = "\n\nURL de origen: " . $originUrl;
        if (mb_strlen($payload['description'] . $originText, 'UTF-8') > 20000) {
            return redirect()->route('gateway_tickets.create')->withInput()->withErrors([
                'description' => 'La descripción junto con los adjuntos y los datos del solicitante supera los 20000 caracteres. Reduzca el texto.',
            ]);
        }
        $payload['description'] .= $originText;
        $local = GatewayTicket::firstOrCreate(['external_id' => $externalId], [
            'user_id' => (string) Auth::id(), 'origin_url' => $originUrl, 'payload' => $payload,
        ]);
        abort_unless($local->user_id === (string) Auth::id(), 403);
        if ($local->payload !== $payload) {
            return redirect()->route('gateway_tickets.create')->withInput()->withErrors(['gateway' => 'Este ticket tiene un envío pendiente. Reintente desde el listado de soporte o cree un ticket nuevo.']);
        }
        try {
            $result = $gateway->send($payload);
            $this->saveSnapshot($local, $result['data']);
        } catch (\RuntimeException $exception) {
            return redirect()->route('gateway_tickets.create')->withInput()->withErrors(['gateway' => $exception->getMessage()]);
        }
        return redirect()->route('gateway_tickets.create')->with('gateway_success', [
            'id' => $result['data']['id'],
            'external_id' => $externalId,
            'duplicate' => !empty($result['meta']['duplicate']),
        ]);
    }

    private function visibleTickets()
    {
        return GatewayTicket::where('user_id', (string) Auth::id());
    }

    public function listing(Request $request)
    {
        abort_if(Auth::user()->id_tipo_usuario == 2, 403);
        $this->validate($request, [
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d' . ($request->input('desde') ? '|after_or_equal:desde' : ''),
            'estado' => 'nullable|in:0,1,2',
        ]);
        $query = $this->visibleTickets();
        if ($request->input('desde')) {
            $query->where('created_at', '>=', $request->input('desde') . ' 00:00:00');
        }
        if ($request->input('hasta')) {
            $query->where('created_at', '<=', $request->input('hasta') . ' 23:59:59');
        }
        return view('ticket_soporte_tecnico.gateway_rows', ['tickets' => $query->orderBy('id', 'desc')->paginate(10)->appends($request->only('desde', 'hasta', 'estado'))]);
    }

    public function refresh(Request $request, GatewayTicketService $gateway, $id)
    {
        abort_if(Auth::user()->id_tipo_usuario == 2, 403);
        $local = $this->visibleTickets()->findOrFail($id);
        try {
            // Los envíos inciertos se recuperan usando exactamente la misma referencia y contenido.
            $result = $local->gateway_id ? $gateway->get($local->gateway_id) : $gateway->send($local->payload);
            $this->saveSnapshot($local, $result['data']);
            return response()->json(['ticket' => $local->snapshot, 'synced_at' => (string) $local->synced_at]);
        } catch (\RuntimeException $exception) {
            return response()->json(['error' => $exception->getMessage()], 502);
        }
    }

    private function saveSnapshot(GatewayTicket $local, array $ticket)
    {
        if (empty($ticket['external_id']) || $ticket['external_id'] !== $local->external_id ||
            !isset($ticket['status']) || !in_array($ticket['status'], ['open', 'assigned', 'resolved'], true) ||
            ($local->gateway_id && $ticket['id'] !== $local->gateway_id)) {
            throw new \RuntimeException('Gateway devolvió un ticket distinto o un estado inválido.');
        }
        $local->update(['gateway_id' => $ticket['id'], 'snapshot' => $ticket, 'synced_at' => date('Y-m-d H:i:s')]);
    }

    public function import(Request $request, GatewayTicketService $gateway)
    {
        abort_unless(Auth::user()->id_tipo_usuario == 1, 403);
        $this->validate($request, ['gateway_id' => 'required|regex:/^[a-fA-F0-9-]{36}$/']);
        try {
            $ticket = $gateway->get($request->input('gateway_id'))['data'];
            $origin = $request->root();
            if (!isset($ticket['description']) || substr($ticket['description'], -strlen("URL de origen: " . $origin)) !== 'URL de origen: ' . $origin) {
                throw new \RuntimeException('El ticket no corresponde a la URL de esta sucursal.');
            }
            $local = GatewayTicket::firstOrCreate(['external_id' => $ticket['external_id']], [
                'user_id' => (string) Auth::id(), 'origin_url' => $origin,
                'payload' => array_intersect_key($ticket, array_flip(['external_id', 'title', 'description', 'priority'])),
            ]);
            $this->saveSnapshot($local, $ticket);
            return response()->json(['ok' => true]);
        } catch (\RuntimeException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }
}
