<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCakeDesignRequest;
use App\Models\CakeDesignRequest;
use App\Services\CakeCustomizer;
use Illuminate\Http\JsonResponse;

class CakeDesignRequestController extends Controller
{
    public function store(StoreCakeDesignRequest $request, CakeCustomizer $customizer): JsonResponse
    {
        $resolved = $customizer->resolve($request->validated('configuration'));
        $design = CakeDesignRequest::create([
            'customer_name' => $request->validated('customer_name'),
            'phone' => $request->validated('phone'),
            'prompt' => $request->validated('prompt'),
            'configuration' => $resolved['configuration'],
            'estimated_price' => $resolved['estimated_price'],
        ]);

        return response()->json([
            'message' => 'Recibimos tu idea. Te vamos a contactar para confirmar los detalles.',
            'id' => $design->id,
            'estimated_price' => $resolved['estimated_price'],
            'configuration' => $resolved['configuration'],
        ], 201);
    }
}
