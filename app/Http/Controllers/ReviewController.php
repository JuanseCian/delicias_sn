<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $approvedReviews = Review::query()->where('approved', true);

        return view('reviews.index', [
            'reviews' => (clone $approvedReviews)->latest()->get(),
            'stats' => [
                'count' => (clone $approvedReviews)->count(),
                'average' => round((float) ((clone $approvedReviews)->avg('rating') ?? 0), 1),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'min:10', 'max:600'],
        ]);

        Review::create([...$data, 'comment' => $data['comment'] ?? '', 'approved' => false]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Valoración recibida.'], 201);
        }

        return back()->with('review_status', 'Gracias por compartir tu opinión. La vamos a revisar antes de publicarla.');
    }
}
