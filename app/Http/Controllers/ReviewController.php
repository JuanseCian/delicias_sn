<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\QueryException;
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

    public function create(string $token): View
    {
        $order = Order::with('review')->where('access_token', $token)->firstOrFail();

        return view('orders.review', [
            'order' => $order,
            'canReview' => $order->canBeReviewed(),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'exists:orders,access_token'],
            'name' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'min:10', 'max:600'],
        ]);

        $order = Order::where('access_token', $data['token'])->firstOrFail();

        if (! $order->canBeReviewed()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ya no podés dejar una opinión para este pedido.'], 409);
            }

            return redirect()->route('reviews.create', ['token' => $order->ensureAccessToken()]);
        }

        try {
            Review::create([
                'order_id' => $order->id,
                'name' => $data['name'],
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? '',
                'approved' => false,
            ]);
        } catch (QueryException) {
            // Envío duplicado simultáneo: otra solicitud ya creó la opinión para este pedido.
            return redirect()->route('reviews.create', ['token' => $order->ensureAccessToken()]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Valoración recibida.'], 201);
        }

        return redirect()->route('reviews.create', ['token' => $order->ensureAccessToken()])
            ->with('review_status', 'Gracias por compartir tu opinión. La vamos a revisar antes de publicarla.');
    }
}
