@php
    $hasFilter = request()->anyFilled(['search', 'category_id', 'status']);
@endphp

<x-admin-layout title="Tour du lịch" subtitle="{{ $tours->total() }} tour trong hệ thống">
    <x-admin.card flush>
        <x-slot:actions>
            <a href="{{ route('admin.tours.create') }}" class="btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                </svg>
                Thêm tour
            </a>
        </x-slot:actions>

        <div class="px-6 py-4 border-b border-gray-100">
            <x-admin.filters :action="route('admin.tours.index')"
                             placeholder="Tên tour hoặc điểm khởi hành..."
                             :has-filter="$hasFilter">
                <x-admin.select name="category_id">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->category_id }}" @selected(request('category_id') == $cat->category_id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-admin.select>

                <x-admin.select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm ẩn</option>
                </x-admin.select>
            </x-admin.filters>
        </div>

        @if ($tours->isEmpty())
            <x-admin.empty icon="tour"
                           :title="$hasFilter ? 'Không tìm thấy tour phù hợp' : 'Chưa có tour nào'"
                           :message="$hasFilter ? 'Thử bỏ bớt bộ lọc để xem thêm.' : 'Tạo tour đầu tiên để bắt đầu bán vé.'">
                <x-slot:action>
                    <a href="{{ $hasFilter ? route('admin.tours.index') : route('admin.tours.create') }}" class="btn-primary btn-sm">
                        {{ $hasFilter ? 'Xoá bộ lọc' : 'Thêm tour' }}
                    </a>
                </x-slot:action>
            </x-admin.empty>
        @else
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="w-16">ID</th>
                            <th>Tour</th>
                            <th>Danh mục</th>
                            <th>Thời gian</th>
                            <th class="text-right">Giá vé</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tours as $tour)
                            <tr>
                                <td class="font-mono text-xs text-gray-400">{{ $tour->tour_id }}</td>

                                <td>
                                    <a href="{{ route('admin.tours.show', $tour) }}"
                                       class="flex items-center gap-3 group max-w-[280px]">
                                        <span class="w-11 h-11 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                            @if ($tour->coverImageUrl())
                                                <img src="{{ $tour->coverImageUrl() }}" alt="" class="w-full h-full object-cover">
                                            @endif
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block font-semibold text-gray-900 truncate group-hover:text-[#2D5A3D]">
                                                {{ $tour->title }}
                                            </span>
                                            @if ($tour->province)
                                                <span class="block text-xs text-gray-400 truncate">{{ $tour->province }}</span>
                                            @endif
                                        </span>
                                    </a>
                                </td>

                                <td>
                                    <x-admin.badge variant="blue">{{ $tour->category?->name ?? '—' }}</x-admin.badge>
                                </td>

                                <td class="max-w-[150px] truncate text-gray-500">
                                    {{ $tour->duration_label ?? $tour->duration_days.' ngày' }}
                                </td>

                                <td class="text-right font-semibold text-gray-900 whitespace-nowrap">
                                    {{ number_format((float) $tour->price) }}₫
                                </td>

                                <td>
                                    <x-admin.badge :variant="$tour->status === 'active' ? 'green' : 'gray'" dot>
                                        {{ $tour->status === 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                                    </x-admin.badge>
                                </td>

                                <td class="whitespace-nowrap">
                                    <x-admin.row-actions>
                                        <x-admin.action :href="route('admin.tours.show', $tour)">Chi tiết</x-admin.action>
                                        <x-admin.action :href="route('admin.tours.edit', $tour)" variant="primary">Sửa</x-admin.action>
                                        <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST"
                                              @submit.prevent="askConfirm($el, 'Xác nhận xoá Tour', 'Bạn có chắc chắn muốn xoá tour &quot;{{ $tour->title }}&quot;? Tất cả lịch khởi hành và ảnh liên quan cũng sẽ bị ảnh hưởng.')">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.action type="submit" variant="danger">Xoá</x-admin.action>
                                        </form>
                                    </x-admin.row-actions>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($tours->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tours->links() }}
                </div>
            @endif
        @endif
    </x-admin.card>
</x-admin-layout>
