<?php

namespace App\Http\Controllers;

use App\Models\CakeDesignRequest;
use App\Models\CustomizerOption;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomizerAdminController extends Controller
{
    public function index(): View
    {
        return view('private.customizer.index', [
            'options' => CustomizerOption::query()->orderBy('kind')->orderBy('sort_order')->orderBy('name')->get(),
            'requests' => CakeDesignRequest::query()->latest()->limit(30)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        CustomizerOption::create($data);

        return to_route('private.customizer.index')->with('success', 'Opción agregada al diseñador.');
    }

    public function update(Request $request, CustomizerOption $option): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:160'],
            'price' => ['required', 'integer', 'min:0', 'max:99999999'],
            'active' => ['nullable', 'boolean'],
        ]);
        $option->update([
            ...$data,
            'active' => $request->boolean('active'),
        ]);

        return to_route('private.customizer.index')->with('success', 'Opción actualizada.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(['size', 'coverage', 'color', 'topping', 'template'])],
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('customizer_options', 'slug')->where(fn (Builder $query): Builder => $query->where('kind', $request->input('kind'))),
            ],
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:160'],
            'price' => ['required', 'integer', 'min:0', 'max:99999999'],
            'metadata' => ['nullable', 'json'],
            'active' => ['nullable', 'boolean'],
        ]);

        return [
            ...$data,
            'metadata' => ! empty($data['metadata']) ? json_decode($data['metadata'], true, 512, JSON_THROW_ON_ERROR) : [],
            'active' => $request->boolean('active', true),
        ];
    }
}
