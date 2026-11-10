<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuggestCakeIdeasRequest;
use App\Models\CustomizerOption;
use App\Services\CakeCustomizer;
use App\Services\CakeDesignAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CakeDesignIdeasController extends Controller
{
    public function store(
        SuggestCakeIdeasRequest $request,
        CakeDesignAiService $ai,
        CakeCustomizer $customizer,
    ): JsonResponse {
        if (! $ai->available()) {
            return response()->json([
                'available' => false,
                'message' => 'El asistente de ideas todavía no está configurado. Podés diseñar tu torta manualmente.',
                'ideas' => [],
            ]);
        }

        $options = CustomizerOption::query()->where('active', true)->get();
        $ideas = [];

        foreach ($ai->suggest($request->validated('prompt'), $options) as $idea) {
            try {
                $resolved = $customizer->resolve($idea['configuration']);
                $ideas[] = [
                    'name' => $idea['name'],
                    'description' => $idea['description'],
                    'configuration' => $resolved['configuration'],
                ];
            } catch (ValidationException) {
                continue;
            }
        }

        return response()->json([
            'available' => true,
            'message' => $ideas === [] ? 'No encontramos una combinación compatible. Probá con otra descripción.' : null,
            'ideas' => $ideas,
        ]);
    }
}
