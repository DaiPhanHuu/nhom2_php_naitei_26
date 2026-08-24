<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Danh sách chuyến đã kết thúc, tách thành nhóm chờ đánh giá và đã đánh giá.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->with(['schedule.tour.images', 'ticketType'])
            ->whereHas('schedule', fn ($query) => $query->whereDate('departure_date', '<', now()->toDateString()))
            ->whereIn('status', ['confirmed', 'completed'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->whereHas('schedule.tour', fn ($q) => $q->where('title', 'like', "%{$search}%"));
            })
            ->latest('booked_at')
            ->get();

        $reviews = $user->reviews()->with(['tour', 'images'])->latest()->get()->keyBy('tour_id');

        // Lọc theo việc đã đánh giá hay chưa (làm sau khi đã nạp review để tránh truy vấn lồng).
        $filter = $request->input('filter');
        if ($filter === 'pending') {
            $bookings = $bookings->reject(fn ($b) => $reviews->has($b->schedule->tour_id))->values();
        } elseif ($filter === 'reviewed') {
            $bookings = $bookings->filter(fn ($b) => $reviews->has($b->schedule->tour_id))->values();
        }

        return view('reviews.index', [
            'bookings' => $bookings,
            'reviews' => $reviews,
        ]);
    }

    public function create(Request $request, Booking $booking): View
    {
        $this->authorizeReview($request, $booking);

        $booking->load(['schedule.tour.images', 'ticketType']);
        $tour = $booking->schedule->tour;

        $review = $request->user()->reviews()->where('tour_id', $tour->tour_id)->with('images')->first();

        return view('reviews.create', [
            'booking' => $booking,
            'tour' => $tour,
            'review' => $review,
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeReview($request, $booking);

        $validated = $request->validate([
            'score' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [], [
            'score' => 'số sao',
            'content' => 'nội dung đánh giá',
            'images.*' => 'ảnh',
        ]);

        $tourId = $booking->schedule->tour_id;
        $userId = $request->user()->user_id;

        // Lưu ảnh ra đĩa trước, ngoài transaction, để không giữ transaction khi ghi file.
        $storedPaths = [];
        foreach ($request->file('images', []) as $file) {
            $storedPaths[] = $file->store('reviews', 'public');
        }

        DB::transaction(function () use ($tourId, $userId, $validated, $storedPaths) {
            $review = Review::updateOrCreate(
                ['user_id' => $userId, 'tour_id' => $tourId],
                [
                    'score' => $validated['score'],
                    'content' => $validated['content'],
                    'status' => 'approved',
                    'approved_at' => now(),
                ]
            );

            $order = (int) $review->images()->max('display_order');

            foreach ($storedPaths as $path) {
                // Lưu đường dẫn tương đối, URL đầy đủ được dựng lúc hiển thị.
                $review->images()->create([
                    'image_url' => 'storage/'.$path,
                    'display_order' => ++$order,
                ]);
            }
        });

        return redirect()
            ->route('reviews.index')
            ->with('status', 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được đăng thành công.');
    }

    /**
     * Chỉ chủ đơn mới được đánh giá, và chỉ khi chuyến đi đã kết thúc.
     */
    private function authorizeReview(Request $request, Booking $booking): void
    {
        abort_unless($booking->user_id === $request->user()->user_id, 403);
        abort_unless(in_array($booking->status, ['confirmed', 'completed'], true), 403, 'Đơn đặt chỗ chưa được xác nhận.');

        $booking->loadMissing('schedule');

        abort_if(
            $booking->schedule->departure_date->startOfDay()->gte(now()->startOfDay()),
            403,
            'Chuyến đi chưa kết thúc nên chưa thể đánh giá.'
        );
    }
}
