<x-admin-layout title="Quản lý Đánh giá" subtitle="Kiểm duyệt đánh giá, bình luận và hình ảnh phản hồi từ du khách">
    <div class="space-y-6">
        <x-admin.card :flush="true">
            <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                <x-admin.filters :action="route('admin.reviews.index')" placeholder="Tên tour hoặc người dùng..." :hasFilter="request()->anyFilled(['search', 'status'])">
                    <x-admin.select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
                        <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Đã từ chối</option>
                    </x-admin.select>
                </x-admin.filters>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-4">Mã ID</th>
                            <th class="px-6 py-4">Người dùng</th>
                            <th class="px-6 py-4">Tour</th>
                            <th class="px-6 py-4">Điểm số</th>
                            <th class="px-6 py-4">Nội dung đánh giá</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4 text-center">Lượt thích</th>
                            <th class="px-6 py-4">Ngày tạo</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($reviews as $review)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-400">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="hover:underline hover:text-[#2D5A3D]">
                                        #{{ $review->review_id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $review->user->username ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 max-w-[200px] truncate">
                                    @if($review->tour)
                                        <a href="{{ route('admin.tours.show', $review->tour) }}" class="font-medium text-gray-900 hover:text-[#2D5A3D] hover:underline">
                                            {{ $review->tour->title }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-amber-400 text-xs font-bold tracking-wider">
                                        {!! str_repeat('★', $review->score) !!}{!! str_repeat('☆', 5 - $review->score) !!}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="block text-xs text-gray-700 hover:text-[#2D5A3D] transition line-clamp-2 leading-relaxed">
                                        {{ $review->content }}
                                    </a>
                                    @if($review->images->isNotEmpty())
                                        <div class="flex gap-1.5 mt-1.5 flex-wrap">
                                            @foreach($review->images as $img)
                                                <a href="{{ route('admin.reviews.show', $review) }}" title="Xem ảnh đính kèm">
                                                    <img src="{{ $img->secure_url }}" alt="Review photo" class="w-7 h-7 object-cover rounded-md border border-gray-200 hover:scale-110 transition" onerror="this.style.display='none'">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($review->status === 'pending')
                                        <x-admin.badge variant="amber" dot>Chờ duyệt</x-admin.badge>
                                    @elseif ($review->status === 'approved')
                                        <x-admin.badge variant="green" dot>Đã duyệt</x-admin.badge>
                                    @elseif ($review->status === 'rejected')
                                        <x-admin.badge variant="red" dot>Đã từ chối</x-admin.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap font-medium text-xs text-gray-700">
                                    <span class="text-rose-500">♥</span> {{ $review->likes_count }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $review->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <x-admin.row-actions>
                                        <x-admin.action :href="route('admin.reviews.show', $review)">Chi tiết</x-admin.action>

                                        @if ($review->status === 'pending' || $review->status === 'rejected')
                                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline-block">
                                                @csrf
                                                <x-admin.action type="submit" variant="primary">Duyệt</x-admin.action>
                                            </form>
                                        @endif

                                        @if ($review->status === 'pending' || $review->status === 'approved')
                                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 text-xs bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition border border-amber-200">Từ chối</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline-block"
                                              @submit.prevent="askConfirm($el, 'Xác nhận xoá Đánh giá', 'Bạn có chắc chắn muốn xoá đánh giá của người dùng {{ $review->user?->username }}? Hành động này không thể hoàn tác.')">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.action type="submit" variant="danger">Xóa</x-admin.action>
                                        </form>
                                    </x-admin.row-actions>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                    Chưa có bài đánh giá nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reviews->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $reviews->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
