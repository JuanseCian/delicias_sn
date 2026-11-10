<?php

namespace App\Services;

use App\Models\CustomizerOption;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CakeCustomizer
{
    /**
     * @param  array<string, mixed>  $configuration
     * @return array{configuration: array<string, mixed>, options: Collection<int, CustomizerOption>, estimated_price: int}
     */
    public function resolve(array $configuration): array
    {
        $sizeId = (int) ($configuration['size_id'] ?? 0);
        $coverageId = (int) ($configuration['coverage_id'] ?? 0);
        $colorIds = array_values(array_unique(array_map('intval', $configuration['color_ids'] ?? [])));
        $toppingIds = array_values(array_unique(array_map('intval', $configuration['topping_ids'] ?? [])));
        $templateId = isset($configuration['template_id']) ? (int) $configuration['template_id'] : null;
        $ids = array_filter([$sizeId, $coverageId, ...$colorIds, ...$toppingIds, $templateId]);
        $options = CustomizerOption::query()
            ->where('active', true)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $this->option($options, $sizeId, 'size_id', 'size');
        $this->option($options, $coverageId, 'coverage_id', 'coverage');

        if ($colorIds === []) {
            throw ValidationException::withMessages(['configuration.color_ids' => 'Elegí al menos un color.']);
        }

        if (count($colorIds) > 2) {
            throw ValidationException::withMessages(['configuration.color_ids' => 'Podés combinar hasta dos colores.']);
        }

        foreach ($colorIds as $colorId) {
            $this->option($options, $colorId, 'configuration.color_ids', 'color');
        }

        if (count($toppingIds) > 8) {
            throw ValidationException::withMessages(['configuration.topping_ids' => 'Elegí hasta ocho decoraciones.']);
        }

        foreach ($toppingIds as $toppingId) {
            $this->option($options, $toppingId, 'configuration.topping_ids', 'topping');
        }

        if ($templateId !== null) {
            $this->option($options, $templateId, 'configuration.template_id', 'template');
        }

        $selectedOptions = collect([$sizeId, $coverageId, ...$colorIds, ...$toppingIds, $templateId])
            ->filter()
            ->map(fn (int $id): CustomizerOption => $options->get($id))
            ->filter();

        return [
            'configuration' => [
                'size_id' => $sizeId,
                'coverage_id' => $coverageId,
                'color_ids' => $colorIds,
                'topping_ids' => $toppingIds,
                'template_id' => $templateId,
            ],
            'options' => $selectedOptions,
            'estimated_price' => (int) $selectedOptions->sum('price'),
        ];
    }

    /**
     * @param  Collection<int, CustomizerOption>  $options
     */
    private function option(Collection $options, int $id, string $attribute, string $kind): CustomizerOption
    {
        $option = $options->get($id);

        if (! $option || $option->kind !== $kind) {
            throw ValidationException::withMessages([
                $attribute => 'Una de las opciones elegidas ya no está disponible.',
            ]);
        }

        return $option;
    }
}
