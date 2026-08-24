<x-admin-layout title="Quản lý Danh mục Tour" subtitle="Phân loại các điểm đến và hình thức trải nghiệm du lịch">
    <div class="space-y-6">
        <x-admin.card :flush="true">
            <x-slot:actions>
                <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-semibold rounded-xl text-sm transition shadow-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Thêm danh mục mới
                </a>
            </x-slot:actions>

            <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                <x-admin.filters :action="route('admin.categories.index')" placeholder="Tìm kiếm theo tên danh mục..." :hasFilter="request('search')">
                </x-admin.filters>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-4">Mã ID</th>
                            <th class="px-6 py-4">Tên danh mục</th>
                            <th class="px-6 py-4">Danh mục cha</th>
                            <th class="px-6 py-4 text-center">Số danh mục con</th>
                            <th class="px-6 py-4 text-center">Số lượng Tour</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-400">#{{ $category->category_id }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="hover:text-[#2D5A3D] hover:underline transition">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($category->parent)
                                        <x-admin.badge variant="gray">{{ $category->parent->name }}</x-admin.badge>
                                    @else
                                        <span class="text-xs text-gray-400 italic">-- Danh mục gốc --</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-admin.badge variant="blue">{{ $category->children_count }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-admin.badge variant="green">{{ $category->tours_count }}</x-admin.badge>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <x-admin.row-actions>
                                        <x-admin.action :href="route('admin.categories.show', $category)">Chi tiết</x-admin.action>
                                        <x-admin.action :href="route('admin.categories.edit', $category)" variant="primary">Sửa</x-admin.action>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block"
                                              @submit.prevent="askConfirm($el, 'Xác nhận xoá Danh mục', 'Bạn có chắc chắn muốn xoá danh mục {{ $category->name }}? Hành động này không thể hoàn tác.')">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.action type="submit" variant="danger">Xóa</x-admin.action>
                                        </form>
                                    </x-admin.row-actions>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    Chưa tìm thấy danh mục tour nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
