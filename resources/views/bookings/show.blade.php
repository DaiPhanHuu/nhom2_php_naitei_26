<x-site-layout title="Chi tiết đặt chỗ">
    <div class="container-narrow max-w-[1100px] py-8">
        <a href="{{ route('bookings.index') }}" class="text-base text-gray-500 hover:text-[#2D5A3D]">← Lịch sử đặt tour</a>

        <div class="mt-4 card-surface overflow-hidden">
            <div class="bg-emerald-50 border-b px-7 py-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="page-title text-[#2D5A3D]">Chi tiết đặt chỗ</h1>
                        <p class="page-subtitle">Mã đặt chỗ #{{ $booking->booking_id }}</p>
                    </div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-white
                        {{ match ($booking->status) {
                            'confirmed' => 'text-[#2D5A3D]',
                            'completed' => 'text-blue-700',
                            'cancelled' => 'text-red-600',
                            default => 'text-amber-700',
                        } }}">
                        @switch($booking->status)
                            @case('pending') Chờ thanh toán @break
                            @case('confirmed') Đã xác nhận @break
                            @case('cancelled') Đã hủy @break
                            @case('completed') Hoàn tất @break
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="p-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-base">
                    <div>
                        <div class="text-gray-400">Tour</div>
                        <div class="font-semibold">{{ $booking->schedule->tour->title }}</div>
                    </div>
                    <div>
                        <div class="text-gray-400">Ngày khởi hành</div>
                        <div class="font-semibold">{{ $booking->schedule->departure_date->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-400">Loại vé</div>
                        <div class="font-semibold">
                            {{ $booking->ticketType ? 'Vé "'.$booking->ticketType->name.'"' : '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-400">Số vé</div>
                        @php
                            $breakdown = $booking->num_adults.' người lớn';
                            if ($booking->num_children) {
                                $breakdown .= ', '.$booking->num_children.' trẻ em';
                            }
                        @endphp
                        <div class="font-semibold">{{ $booking->num_adults + $booking->num_children }} vé
                            <span class="text-gray-400 font-normal">({{ $breakdown }})</span>
                        </div>
                    </div>
                    <div class="sm:col-span-2 pt-4 border-t flex items-baseline justify-between">
                        <span class="text-gray-400">Tổng tiền</span>
                        <span class="text-2xl font-bold text-[#2D5A3D]">{{ number_format((float) $booking->total_amount) }} VND</span>
                    </div>
                    @if ($booking->note)
                        <div class="sm:col-span-2">
                            <div class="text-gray-400">Ghi chú</div>
                            <div>{{ $booking->note }}</div>
                        </div>
                    @endif
                </div>

                {{-- Hành khách --}}
                @if ($booking->details->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="card-title">Thông tin người đi ({{ $booking->details->count() }})</h2>
                        <div class="mt-3 divide-y border rounded-xl overflow-hidden">
                            @foreach ($booking->details as $detail)
                                <div class="p-4 flex flex-wrap items-center gap-4 text-base">
                                    <span class="w-7 h-7 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center shrink-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="font-semibold">{{ $detail->name }}</span>
                                    @if ($detail->is_booker)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#2D5A3D] text-xs font-semibold">Người đặt</span>
                                    @endif
                                    @if ($detail->age)
                                        <span class="text-gray-500">{{ $detail->age }} tuổi</span>
                                    @endif
                                    @if ($detail->phone)
                                        <span class="text-gray-500">{{ $detail->phone }}</span>
                                    @endif
                                    @if ($detail->seat_no)
                                        <span class="text-gray-500">Ghế {{ $detail->seat_no }}</span>
                                    @endif
                                    <span class="ml-auto font-semibold">{{ number_format((float) $detail->price) }}₫</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Thanh toán --}}
                @if ($booking->payment)
                    <div class="mt-8">
                        <h2 class="card-title">Thanh toán</h2>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-5 text-base border rounded-xl p-5">
                            <div>
                                <div class="text-gray-400">Cổng thanh toán</div>
                                <div class="font-semibold">{{ ucfirst($booking->payment->gateway) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-400">Trạng thái</div>
                                <div class="font-semibold {{ $booking->payment->status === 'success' ? 'text-[#2D5A3D]' : 'text-amber-700' }}">
                                    {{ match ($booking->payment->status) {
                                        'success' => 'Thành công',
                                        'failed' => 'Thất bại',
                                        'refunded' => 'Đã hoàn tiền',
                                        default => 'Chờ thanh toán',
                                    } }}
                                </div>
                            </div>
                            @if ($booking->payment->paid_at)
                                <div>
                                    <div class="text-gray-400">Thanh toán lúc</div>
                                    <div class="font-semibold">{{ $booking->payment->paid_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($booking->status === 'cancelled' && $booking->cancelled_at)
                    <p class="mt-6 text-sm text-gray-500">Đã hủy lúc {{ $booking->cancelled_at->format('d/m/Y H:i') }}</p>
                @endif

                {{-- Đánh giá tour --}}
                @if (in_array($booking->status, ['confirmed', 'completed'], true))
                    <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100 border border-emerald-200 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                    <span>⭐ Đánh giá chuyến đi</span>
                                    @if ($hasReviewed ?? false)
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-800">Đã đánh giá</span>
                                    @endif
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ ($hasReviewed ?? false)
                                        ? 'Cảm ơn bạn đã viết đánh giá cho tour này! Đánh giá của bạn giúp ích rất nhiều cho cộng đồng.'
                                        : 'Hãy chia sẻ cảm nhận, hình ảnh thực tế và đánh giá chất lượng chuyến đi của bạn.' }}
                                </p>
                            </div>
                            <div class="shrink-0">
                                @if ($hasReviewed ?? false)
                                    <a href="{{ route('tours.show', $booking->schedule->tour) }}" class="px-4 py-2 bg-white text-[#2D5A3D] hover:bg-emerald-50 border border-emerald-200 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition inline-flex items-center gap-1.5">
                                        Xem trang Tour
                                    </a>
                                @else
                                    <a href="{{ route('reviews.create', $booking) }}" class="btn-primary btn-sm inline-flex items-center gap-1.5 shadow">
                                        ✍️ Viết đánh giá ngay
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($booking->status === 'pending')
                        <a href="{{ route('bookings.pay', $booking) }}" class="btn-accent">
                            Thanh toán ngay (Quét mã QR)
                        </a>
                    @endif

                    @if (in_array($booking->status, ['pending', 'confirmed'], true))
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                              onsubmit="return confirm('Bạn có chắc muốn hủy đơn đặt tour này?');">
                            @csrf
                            <button type="submit" class="btn px-6 py-3 border border-red-300 text-red-600 hover:bg-red-50">
                                Hủy đặt tour
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('bookings.index') }}" class="btn-ghost">Danh sách đặt tour</a>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
