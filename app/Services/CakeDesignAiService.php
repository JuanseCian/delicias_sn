<?php

namespace App\Services;

use App\Models\CustomizerOption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class CakeDesignAiService
{
    public function available(): bool
    {
        return filled(config('services.cake_ai.provider')) && filled(config('services.cake_ai.key'));
    }

    /**
     * @param  Collection<int, CustomizerOption>  $options
     * @return array<int, array{name: string, description: string, configuration: array<string, mixed>}>
     */
    public function suggest(string $prompt, Collection $options): array
    {
        if (! $this->available()) {
            return [];
        }

        $catalog = $options->map(fn (CustomizerOption $option): array => [
            'id' => $option->id,
            'kind' => $option->kind,
            'slug' => $option->slug,
            'name' => $option->name,
            'metadata' => $option->metadata ?? [],
        ])->values()->all();

        try {
            $response = Http::withToken(config('services.cake_ai.key'))
                ->acceptJson()
                ->timeout(20)
                ->post(config('services.cake_ai.endpoint'), [
                    'model' => config('services.cake_ai.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Sos un asistente de diseño de tortas. Usá solamente opciones del catálogo permitido. Devolvé JSON válido con hasta tres ideas. Cada configuration debe tener size_id, coverage_id, color_ids, topping_ids y template_id. No inventes IDs.',
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode(['prompt' => $prompt, 'allowed_options' => $catalog], JSON_THROW_ON_ERROR),
                        ],
                    ],
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'cake_design_ideas',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'ideas' => [
                                        'type' => 'array',
                                        'maxItems' => 3,
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'name' => ['type' => 'string'],
                                                'description' => ['type' => 'string'],
                                                'configuration' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'size_id' => ['type' => 'integer'],
                                                        'coverage_id' => ['type' => 'integer'],
                                                        'color_ids' => ['type' => 'array', 'maxItems' => 2, 'items' => ['type' => 'integer']],
                                                        'topping_ids' => ['type' => 'array', 'maxItems' => 8, 'items' => ['type' => 'integer']],
                                                        'template_id' => ['type' => ['integer', 'null']],
                                                    ],
                                                    'required' => ['size_id', 'coverage_id', 'color_ids', 'topping_ids', 'template_id'],
                                                    'additionalProperties' => false,
                                                ],
                                            ],
                                            'required' => ['name', 'description', 'configuration'],
                                            'additionalProperties' => false,
                                        ],
                                    ],
                                ],
                                'required' => ['ideas'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ])
                ->throw();

            $content = data_get($response->json(), 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true, 512, JSON_THROW_ON_ERROR) : [];
            $ideas = is_array($decoded['ideas'] ?? null) ? $decoded['ideas'] : [];

            return array_values(array_filter(array_slice($ideas, 0, 3), static fn (mixed $idea): bool => is_array($idea)
                && is_string($idea['name'] ?? null)
                && is_string($idea['description'] ?? null)
                && is_array($idea['configuration'] ?? null)));
        } catch (Throwable) {
            return [];
        }
    }
}
