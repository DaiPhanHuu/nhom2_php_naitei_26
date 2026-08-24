@php
    $statusLabels = [
        'pending' => ['Chờ xử lý', 'badge-amber'],
        'confirmed' => ['Đã xác nhận', 'badge-green'],
        'completed' => ['Hoàn tất', 'badge-blue'],
        'cancelled' => ['Đã hủy', 'badge-red'],
    ];
@endphp

<x-admin-layout title="Dashboard" subtitle="Tổng quan hoạt động hệ thống">
    {{-- Thẻ thống kê --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <path d="M3 3v18h18" stroke-linecap="round"/><path d="m7 14 4-4 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <div class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight leading-none whitespace-nowrap overflow-x-auto">{{ number_format((float) $totalRevenue) }}₫</div>
                <div class="stat-label mt-1.5">Tổng doanh thu</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <div class="stat-value">{{ number_format($totalBookings) }}</div>
                <div class="stat-label">Tổng đơn đặt chỗ</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div>
                <div class="stat-value">{{ number_format($pendingBookingsCount) }}</div>
                <div class="stat-label">Đơn chờ xử lý</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div>
                <div class="stat-value">{{ number_format($activeToursCount) }}</div>
                <div class="stat-label">Tour đang mở bán</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-indigo-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <div class="stat-value">{{ number_format($totalUsersCount) }}</div>
                <div class="stat-label">Người dùng</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7">
                    <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>
                </svg>
            </span>
            <div>
                <div class="stat-value">{{ number_format($pendingReviewsCount) }}</div>
                <div class="stat-label">Đánh giá chờ duyệt</div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-6 items-start">
        {{-- Đơn mới nhất --}}
        <div class="admin-card overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-5 py-4 border-b">
                <h2 class="font-bold">Đơn đặt chỗ mới nhất</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-[#2D5A3D] hover:underline">
                    Xem tất cả →
                </a>
            </div>

            @if ($latestBookings->isEmpty())
                <p class="p-8 text-center text-gray-400">Chưa có đơn đặt chỗ nào.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Khách hàng</th>
                                <th>Tour</th>
                                <th class="text-right">Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestBookings as $booking)
                                @php [$label, $badge] = $statusLabels[$booking->status] ?? [$booking->status, 'badge-gray']; @endphp
                                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                                    <td class="text-gray-400">#{{ $booking->booking_id }}</td>
                                    <td class="font-medium">{{ $booking->user?->username ?? '—' }}</td>
                                    <td class="max-w-[220px] truncate">{{ $booking->schedule?->tour?->title ?? '—' }}</td>
                                    <td class="text-right font-semibold">{{ number_format((float) $booking->total_amount) }}₫</td>
                                    <td><span class="{{ $badge }}">{{ $label }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Đánh giá chờ duyệt --}}
        <div class="admin-card overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-5 py-4 border-b">
                <h2 class="font-bold">Đánh giá chờ duyệt</h2>
                <a href="{{ route('admin.reviews.index') }}" class="text-sm font-semibold text-[#2D5A3D] hover:underline">
                    Xem tất cả →
                </a>
            </div>

            @if ($latestPendingReviews->isEmpty())
                <p class="p-8 text-center text-gray-400">Không có đánh giá nào chờ duyệt.</p>
            @else
                <div class="divide-y">
                    @foreach ($latestPendingReviews as $review)
                        <a href="{{ route('admin.reviews.show', $review) }}" class="block p-5 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($review->user?->username ?? '?', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold truncate">{{ $review->user?->username ?? '—' }}</span>
                                        @if ($review->score)
                                            <x-star-rating :score="$review->score" />
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 truncate">{{ $review->tour?->title }}</div>
                                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $review->content }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
