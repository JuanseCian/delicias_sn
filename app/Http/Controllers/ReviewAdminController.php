<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewAdminController extends Controller
{
    public function index(): View
    {
        return view('private.reviews.index', [
            'reviews' => Review::query()->with('order')->latest()->get(),
        ]);
    }

    public function update(Review $review): RedirectResponse
    {
        $review->update(['approved' => ! $review->approved]);

        return back()->with('status', $review->approved ? 'Valoración publicada.' : 'Valoración ocultada.');
    }
}
