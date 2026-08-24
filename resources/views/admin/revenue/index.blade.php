<x-admin-layout title="Báo cáo Doanh thu" subtitle="Thống kê tổng quan doanh thu, xu hướng theo tháng và phân tích hiệu quả kinh doanh">
    <div class="space-y-6">
        {{-- Bộ lọc thời gian --}}
        <x-admin.card>
            <form method="GET" action="{{ route('admin.revenue.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="from" class="block text-xs font-semibold text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" name="from" id="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                           class="h-10 text-xs rounded-xl border-gray-200 bg-gray-50/60 focus:bg-white focus:border-[#2D5A3D] focus:ring-[#2D5A3D]">
                </div>
                <div>
                    <label for="to" class="block text-xs font-semibold text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="to" id="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                           class="h-10 text-xs rounded-xl border-gray-200 bg-gray-50/60 focus:bg-white focus:border-[#2D5A3D] focus:ring-[#2D5A3D]">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-5 rounded-xl bg-[#2D6A2D] hover:bg-[#245524] text-white font-semibold text-xs transition shadow-sm">
                        Lọc báo cáo
                    </button>
                    @if (request()->anyFilled(['from', 'to']))
                        <a href="{{ route('admin.revenue.index') }}" class="h-10 px-4 inline-flex items-center rounded-xl text-xs text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                            Xoá lọc
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        {{-- Thống kê tổng quan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card">
                <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="text-xl sm:text-2xl font-extrabold text-[#2D5A3D] tracking-tight leading-none whitespace-nowrap overflow-x-auto">{{ number_format($totalRevenue, 0, ',', '.') }}₫</div>
                    <div class="stat-label mt-1.5">Tổng doanh thu</div>
                </div>
            </div>

            <div class="stat-card">
                <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="text-xl sm:text-2xl font-extrabold text-blue-900 tracking-tight leading-none">{{ number_format($totalBookings) }}</div>
                    <div class="stat-label mt-1.5">Tổng đơn đặt chỗ</div>
                </div>
            </div>

            <div class="stat-card">
                <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="text-xl sm:text-2xl font-extrabold text-[#2D5A3D] tracking-tight leading-none">{{ $totalBookings > 0 ? round(($confirmedBookings / $totalBookings) * 100, 1) : 0 }}%</div>
                    <div class="stat-label mt-1.5">Tỷ lệ xác nhận</div>
                </div>
            </div>

            <div class="stat-card">
                <span class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="text-xl sm:text-2xl font-extrabold text-purple-900 tracking-tight leading-none whitespace-nowrap overflow-x-auto">{{ number_format($avgBookingValue, 0, ',', '.') }}₫</div>
                    <div class="stat-label mt-1.5">Giá trị TB/đơn</div>
                </div>
            </div>
        </div>

        {{-- Xu hướng theo tháng và Top Tour --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Xu hướng theo tháng --}}
            <x-admin.card title="Xu hướng doanh thu (6 tháng qua)" subtitle="Biểu đồ phân bổ doanh thu theo từng tháng">
                @if(empty($monthlyTrend))
                    <p class="text-gray-400 text-sm italic py-4">Chưa có dữ liệu doanh thu.</p>
                @else
                    <div class="space-y-4">
                        @foreach($monthlyTrend as $trend)
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="text-gray-900">{{ $trend['month'] }}</span>
                                    <span class="text-[#2D5A3D] font-bold">{{ number_format($trend['revenue'], 0, ',', '.') }}₫</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    @php $width = $maxMonthlyRevenue > 0 ? ($trend['revenue'] / $maxMonthlyRevenue) * 100 : 0; @endphp
                                    <div class="bg-gradient-to-r from-emerald-500 to-[#2D5A3D] h-2.5 rounded-full transition-all duration-500" style="width: {{ $width }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>

            {{-- Doanh thu theo Tour --}}
            <x-admin.card title="Top Tour doanh thu cao" subtitle="Danh sách các cung đường mang lại doanh thu tốt nhất" :flush="true">
                @if($topTours->isEmpty())
                    <p class="p-8 text-center text-gray-400 text-sm">Chưa có dữ liệu doanh thu tour.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3.5">#</th>
                                    <th class="px-5 py-3.5">Tên Tour</th>
                                    <th class="px-5 py-3.5 text-center">Số đơn</th>
                                    <th class="px-5 py-3.5 text-right">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topTours as $index => $tour)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-5 py-3.5 text-xs text-gray-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3.5 font-semibold text-gray-900 text-xs truncate max-w-[200px]">
                                            {{ $tour->title }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center text-xs font-semibold text-gray-700">
                                            {{ $tour->booking_count }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-xs font-bold text-[#2D5A3D] whitespace-nowrap">
                                            {{ number_format($tour->total_revenue, 0, ',', '.') }}₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin.card>
        </div>

        {{-- Giao dịch gần đây --}}
        <x-admin.card title="Giao dịch thanh toán gần đây" subtitle="Nhật ký các giao dịch chuyển khoản thành công mới nhất" :flush="true">
            @if($recentTransactions->isEmpty())
                <p class="p-8 text-center text-gray-400 text-sm">Chưa có giao dịch nào.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th class="px-6 py-4">Mã Giao dịch</th>
                                <th class="px-6 py-4">Khách hàng</th>
                                <th class="px-6 py-4">Tour</th>
                                <th class="px-6 py-4 text-right">Số tiền</th>
                                <th class="px-6 py-4 text-right">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4 font-mono text-xs text-gray-900 font-medium">
                                        {{ Str::limit($transaction->gateway_txn_id, 18) ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900 text-xs">
                                        {{ $transaction->booking->user->username ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-700 max-w-[240px] truncate">
                                        {{ $transaction->booking->schedule->tour->title ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-xs text-[#2D5A3D] whitespace-nowrap">
                                        {{ number_format($transaction->amount, 0, ',', '.') }}₫
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-500 whitespace-nowrap">
                                        {{ optional($transaction->paid_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
