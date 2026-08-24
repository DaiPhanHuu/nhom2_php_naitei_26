<x-admin-layout title="Quản lý Người dùng" subtitle="Danh sách toàn bộ tài khoản thành viên và phân quyền hệ thống">
    <div class="space-y-6">
        <x-admin.card :flush="true">
            <x-slot:actions>
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-semibold rounded-xl text-sm transition shadow-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Thêm người dùng mới
                </a>
            </x-slot:actions>

            <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                <x-admin.filters :action="route('admin.users.index')" placeholder="Tìm theo tên đăng nhập, email..." :hasFilter="request()->anyFilled(['search', 'role', 'status'])">
                    <x-admin.select name="role">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="user" @selected(request('role') === 'user')>User</option>
                    </x-admin.select>

                    <x-admin.select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Ngừng hoạt động</option>
                        <option value="banned" @selected(request('status') === 'banned')>Đã khoá</option>
                    </x-admin.select>
                </x-admin.filters>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-4">Mã ID</th>
                            <th class="px-6 py-4">Tên đăng nhập</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Cấu hình Vai trò &amp; Trạng thái</th>
                            <th class="px-6 py-4">Đăng nhập cuối</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-400">#{{ $user->user_id }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $user->username }}</span>
                                        @if($user->isSuperAdmin())
                                            <span class="px-2.5 py-0.5 text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 rounded-full shadow-xs">👑 Super Admin</span>
                                        @elseif($user->role === 'admin')
                                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-emerald-50 text-[#2D5A3D] border border-emerald-200 rounded-full">🛡️ Admin</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if($user->isSuperAdmin())
                                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">Tối cao (Khóa chỉnh sửa)</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <select name="role" class="rounded-xl border-gray-200 text-xs py-1.5 px-2.5 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] bg-white shadow-2xs">
                                                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                                <option value="user" @selected($user->role === 'user')>User</option>
                                            </select>

                                            <select name="status" class="rounded-xl border-gray-200 text-xs py-1.5 px-2.5 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] bg-white shadow-2xs">
                                                <option value="active" @selected($user->status === 'active')>Hoạt động</option>
                                                <option value="inactive" @selected($user->status === 'inactive')>Ngừng hoạt động</option>
                                                <option value="banned" @selected($user->status === 'banned')>Đã khoá</option>
                                            </select>

                                            <button type="submit" class="px-2.5 py-1 text-xs bg-emerald-50 hover:bg-emerald-100 text-[#2D5A3D] font-bold rounded-lg transition border border-emerald-200">Lưu</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Chưa đăng nhập' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($user->isSuperAdmin())
                                        <span class="text-xs text-gray-400 italic">Hệ thống bảo vệ</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              @submit.prevent="askConfirm($el, 'Xác nhận xoá tài khoản', 'Bạn có chắc chắn muốn xoá tài khoản {{ $user->username }} ({{ $user->email }})? Hành động này không thể hoàn tác.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg font-bold transition">Xoá</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    Không tìm thấy tài khoản người dùng nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
