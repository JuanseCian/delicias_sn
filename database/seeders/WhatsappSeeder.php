<?php

namespace Database\Seeders;

use App\Models\WhatsappAutomation;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappSeeder extends Seeder
{
    public function run(): void
    {
        $reviewTemplate = WhatsappTemplate::updateOrCreate(
            ['key' => 'review_after_delivery'],
            [
                'name' => 'Invitación a dejar una reseña',
                'body' => "¡Hola, {{nombre}}, ¿cómo estás?! 💗 Esperamos que hayas disfrutado tu pedido. Nos encantaría conocer tu opinión, por eso te invitamos a dejarnos una pequeña valoración en el siguiente link.\n\n{{enlace_resena}}\n\n¡Gracias por elegir Delicias SN! 🧁",
                'is_active' => true,
            ],
        );

        WhatsappAutomation::updateOrCreate(
            ['key' => 'review_after_delivery'],
            [
                'name' => 'Reseña después de la entrega',
                'trigger' => 'pedido_entregado',
                'template_id' => $reviewTemplate->id,
                'delay_minutes' => 0,
                'is_enabled' => true,
            ],
        );

        WhatsappSetting::put('global_enabled', (bool) config('services.whatsapp.enabled', false));
    }
}
