<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappMessageJob;
use App\Models\WhatsappAutomation;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use App\Services\EvolutionApiService;
use App\Services\WhatsappAutomationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class WhatsappAdminController extends Controller
{
    public function index(Request $request, EvolutionApiService $evolution): View
    {
        $connection = null;
        $connectionError = null;

        try {
            $connection = $evolution->connectionState();
        } catch (Throwable $exception) {
            $connectionError = $exception->getMessage();
        }

        $messages = WhatsappMessage::query()
            ->with('order')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('recipient', 'like', '%'.$search.'%')
                        ->orWhere('provider_message_id', 'like', '%'.$search.'%')
                        ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('code', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('private.whatsapp.index', [
            'connection' => $connection,
            'connectionError' => $connectionError,
            'globalEnabled' => app(WhatsappAutomationService::class)->isEnabled(),
            'templates' => WhatsappTemplate::query()->orderBy('name')->get(),
            'automations' => WhatsappAutomation::query()->with('template')->orderBy('name')->get(),
            'messages' => $messages,
        ]);
    }

    public function connect(EvolutionApiService $evolution): RedirectResponse
    {
        try {
            $evolution->connect();
            $evolution->setWebhook($this->webhookUrl());

            return back()->with('status', 'Solicitud de conexión enviada. Actualizá el estado para ver el QR.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['whatsapp' => $exception->getMessage()]);
        }
    }

    public function qr(EvolutionApiService $evolution): JsonResponse
    {
        try {
            return response()->json($evolution->connect());
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function logout(EvolutionApiService $evolution): RedirectResponse
    {
        try {
            $evolution->logout();

            return back()->with('status', 'La instancia de WhatsApp fue desconectada.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['whatsapp' => $exception->getMessage()]);
        }
    }

    public function toggle(Request $request): RedirectResponse
    {
        $enabled = $request->boolean('enabled');
        WhatsappSetting::put('global_enabled', $enabled);

        return back()->with('status', $enabled ? 'Automatizaciones de WhatsApp habilitadas.' : 'Automatizaciones de WhatsApp deshabilitadas.');
    }

    public function updateAutomation(Request $request, WhatsappAutomation $automation): RedirectResponse
    {
        $data = $request->validate([
            'is_enabled' => ['required', 'boolean'],
            'template_id' => ['required', Rule::exists('whatsapp_templates', 'id')],
            'delay_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
        ]);
        $automation->update($data);

        return back()->with('status', 'Automatización actualizada.');
    }

    public function updateTemplate(Request $request, WhatsappTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $template->update([
            'body' => $data['body'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return back()->with('status', 'Plantilla actualizada.');
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:40'],
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $message = WhatsappMessage::create([
            'direction' => 'outbound',
            'type' => 'text',
            'recipient' => $data['recipient'],
            'body' => $data['body'],
            'status' => 'queued',
        ]);
        SendWhatsappMessageJob::dispatch($message->id)->onConnection('whatsapp')->onQueue('whatsapp');

        return back()->with('status', 'Mensaje de prueba enviado a la cola whatsapp.');
    }

    public function retry(WhatsappMessage $message): RedirectResponse
    {
        abort_unless($message->status === 'failed', 404);

        $message->update(['status' => 'queued', 'error_message' => null]);
        SendWhatsappMessageJob::dispatch($message->id)->onConnection('whatsapp')->onQueue('whatsapp');

        return back()->with('status', 'Mensaje reprogramado en la cola whatsapp.');
    }

    private function webhookUrl(): string
    {
        $url = route('whatsapp.webhook');
        $secret = config('services.evolution.webhook_secret');

        return filled($secret) ? $url.'?token='.urlencode((string) $secret) : $url;
    }
}
