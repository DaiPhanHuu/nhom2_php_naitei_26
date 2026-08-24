<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Lịch sử đặt chỗ của người dùng, có tìm kiếm và lọc.
     */
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['schedule.tour.images', 'ticketType', 'payment'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->whereHas('schedule.tour', fn ($q) => $q->where('title', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->input('time') === 'upcoming', fn ($q) => $q->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '>=', now()->toDateString())
            ))
            ->when($request->input('time') === 'past', fn ($q) => $q->whereHas(
                'schedule',
                fn ($s) => $s->whereDate('departure_date', '<', now()->toDateString())
            ))
            ->latest('booked_at')
            ->paginate(10)
            ->withQueryString();

        $userReviewTourIds = \App\Models\Review::where('user_id', $request->user()->user_id)
            ->pluck('tour_id')
            ->toArray();

        return view('bookings.index', [
            'bookings' => $bookings,
            'userReviewTourIds' => $userReviewTourIds,
            'statusCounts' => $request->user()->bookings()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function create(Tour $tour): View
    {
        $tour->load([
            'ticketTypes',
            'schedules' => fn ($query) => $query->where('departure_date', '>=', now()->toDateString())
                ->orderBy('departure_date'),
        ]);

        return view('bookings.create', ['tour' => $tour]);
    }

    /**
     * Tạo đơn đặt chỗ: giá theo loại vé, trẻ dưới 12 tuổi tính nửa giá.
     */
    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:tour_schedules,schedule_id'],
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,ticket_type_id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'passengers.*.phone' => ['nullable', 'string', 'max:30'],
            'passengers.*.seat_no' => ['nullable', 'string', 'max:20'],
        ]);

        $ticketType = $tour->ticketTypes()->findOrFail($validated['ticket_type_id']);
        $passengers = $validated['passengers'];

        $booking = DB::transaction(function () use ($tour, $validated, $ticketType, $passengers, $request) {
            $schedule = $tour->schedules()->lockForUpdate()->find($validated['schedule_id']);

            if (! $schedule || $schedule->available_slots < count($passengers)) {
                return null;
            }

            $unitPrice = (float) $ticketType->price;
            $numAdults = 0;
            $numChildren = 0;
            $totalAmount = 0;
            $details = [];

            foreach ($passengers as $index => $passenger) {
                $age = isset($passenger['age']) ? (int) $passenger['age'] : null;
                $isChild = $age !== null && $age < 12;

                $isChild ? $numChildren++ : $numAdults++;
                $price = $isChild ? $unitPrice * 0.5 : $unitPrice;
                $totalAmount += $price;

                $details[] = [
                    'name' => trim($passenger['full_name']),
                    'age' => $age ?? 0,
                    'price' => $price,
                    'phone' => $passenger['phone'] ?? null,
                    'seat_no' => $passenger['seat_no'] ?? null,
                    'is_booker' => $index === 0,
                ];
            }

            $booking = Booking::create([
                'user_id' => $request->user()->user_id,
                'schedule_id' => $schedule->schedule_id,
                'ticket_type_id' => $ticketType->ticket_type_id,
                'num_adults' => $numAdults,
                'num_children' => $numChildren,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'note' => $validated['note'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($details as $detail) {
                $booking->details()->create($detail);
            }

            $schedule->decrement('available_slots', count($passengers));

            return $booking;
        });

        if (! $booking) {
            return back()->withInput()->with('error', 'Chuyến đi này không còn đủ chỗ trống.');
        }

        return redirect()
            ->route('bookings.pay', $booking)
            ->with('status', 'Đặt chỗ thành công! Vui lòng thanh toán để hoàn tất.');
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->user_id === request()->user()->user_id, 403);

        $booking->load(['schedule.tour', 'ticketType', 'details', 'payment']);

        $hasReviewed = \App\Models\Review::where('user_id', request()->user()->user_id)
            ->where('tour_id', $booking->schedule->tour_id)
            ->exists();

        return view('bookings.show', [
            'booking' => $booking,
            'hasReviewed' => $hasReviewed,
        ]);
    }

    /**
     * Huỷ đơn và trả lại chỗ đã giữ cho lịch khởi hành.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === request()->user()->user_id, 403);

        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            return back()->withErrors(['status' => 'Không thể hủy đơn đặt tour này.']);
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $booking->schedule()->increment('available_slots', $booking->num_adults + $booking->num_children);
        });

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', 'Đã hủy đơn đặt chỗ.');
    }
}
