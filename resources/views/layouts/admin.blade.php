@php
    $navGroups = [
        [
            'label' => 'Tổng quan',
            'items' => [
                ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'admin.revenue.index', 'active' => 'admin.revenue.*', 'label' => 'Doanh thu', 'icon' => 'revenue'],
            ],
        ],
        [
            'label' => 'Kinh doanh',
            'items' => [
                ['route' => 'admin.bookings.index', 'active' => 'admin.bookings.*', 'label' => 'Đơn đặt chỗ', 'icon' => 'booking'],
                ['route' => 'admin.tours.index', 'active' => 'admin.tours.*', 'label' => 'Tour', 'icon' => 'tour'],
                ['route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'label' => 'Danh mục', 'icon' => 'category'],
            ],
        ],
        [
            'label' => 'Cộng đồng',
            'items' => [
                ['route' => 'admin.reviews.index', 'active' => 'admin.reviews.*', 'label' => 'Đánh giá', 'icon' => 'review'],
                ['route' => 'admin.users.index', 'active' => 'admin.users.*', 'label' => 'Người dùng', 'icon' => 'user'],
            ],
        ],
    ];

    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'revenue' => '<path d="M3 3v18h18" stroke-linecap="round"/><path d="m7 14 4-4 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/>',
        'booking' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/>',
        'tour' => '<path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'category' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'review' => '<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Quản trị' }} — Sun* Booking Tour</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=livvic:400,500,600,700,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-900" style="font-family: 'Livvic', sans-serif;">
        <div class="min-h-screen flex" x-data="{
            sidebar: false,
            confirmOpen: false,
            confirmTitle: 'Xác nhận thao tác',
            confirmMessage: 'Bạn có chắc chắn muốn thực hiện thao tác này?',
            confirmForm: null,
            toasts: [],
            addToast(type, message) {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, type, message });
                setTimeout(() => this.removeToast(id), 4500);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
            askConfirm(form, title, message) {
                this.confirmForm = form;
                this.confirmTitle = title || 'Xác nhận xóa';
                this.confirmMessage = message || 'Hành động này không thể hoàn tác. Bạn có chắc muốn tiếp tục?';
                this.confirmOpen = true;
            },
            doConfirm() {
                if (this.confirmForm) {
                    this.confirmForm.submit();
                }
                this.confirmOpen = false;
            }
        }"
        x-init="
            @if (session('status')) addToast('success', '{{ e(session('status')) }}'); @endif
            @if (session('success')) addToast('success', '{{ e(session('success')) }}'); @endif
            @if (session('error')) addToast('error', '{{ e(session('error')) }}'); @endif
            @if (session('warning')) addToast('warning', '{{ e(session('warning')) }}'); @endif
        ">

            {{-- Floating Toast Notifications --}}
            <div class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-md w-full pointer-events-none px-4" x-cloak>
                <template x-for="toast in toasts" :key="toast.id">
                    <div x-show="true"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="pointer-events-auto flex items-start gap-3.5 p-4 rounded-2xl shadow-xl border text-sm font-medium transition-all"
                         :class="{
                             'bg-emerald-900 text-white border-emerald-700': toast.type === 'success',
                             'bg-rose-900 text-white border-rose-700': toast.type === 'error',
                             'bg-amber-800 text-white border-amber-600': toast.type === 'warning',
                             'bg-slate-900 text-white border-slate-700': toast.type === 'info'
                         }">
                        <template x-if="toast.type === 'success'">
                            <div class="w-6 h-6 rounded-full bg-emerald-600/60 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <div class="w-6 h-6 rounded-full bg-rose-600/60 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-rose-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <div class="w-6 h-6 rounded-full bg-amber-600/60 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-amber-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        </template>
                        <div class="flex-1 pt-0.5 leading-relaxed" x-text="toast.message"></div>
                        <button type="button" @click="removeToast(toast.id)" class="text-white/70 hover:text-white shrink-0 p-0.5 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Confirm Delete/Action Modal --}}
            <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="confirmOpen"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="confirmOpen = false"
                         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="confirmOpen"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="sm:flex sm:items-start gap-4">
                                <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 sm:mx-0">
                                    <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="confirmTitle"></h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 leading-relaxed" x-text="confirmMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                            <button type="button" @click="doConfirm()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-rose-600 text-base font-bold text-white hover:bg-rose-700 focus:outline-none sm:w-auto sm:text-sm transition shadow-rose-200">
                                Xác nhận xóa
                            </button>
                            <button type="button" @click="confirmOpen = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm transition">
                                Hủy bỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Sidebar --}}
            <aside class="fixed lg:static inset-y-0 left-0 z-40 w-72 shrink-0 bg-[#1B2A20] text-gray-300 flex flex-col
                          transition-transform lg:translate-x-0"
                   :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
                <div class="h-[72px] flex items-center gap-3 px-6 border-b border-white/10">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-lg bg-[#F4D03F] text-[#1B2A20] font-extrabold flex items-center justify-center">S</span>
                        <span class="text-white font-bold leading-tight">
                            Sun* Admin
                            <span class="block text-xs font-normal text-gray-400">Booking Tour</span>
                        </span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto py-5">
                    @foreach ($navGroups as $group)
                        <div class="px-6 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            {{ $group['label'] }}
                        </div>

                        @foreach ($group['items'] as $item)
                            @continue(! Route::has($item['route']))
                            @php $isActive = request()->routeIs($item['active']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="relative flex items-center gap-3 px-6 py-2.5 text-[15px] transition
                                      {{ $isActive ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                                @if ($isActive)
                                    <span class="absolute left-0 inset-y-1 w-1 rounded-r bg-[#F4D03F]"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.6" class="w-5 h-5 shrink-0">
                                    {!! $icons[$item['icon']] !!}
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    @endforeach
                </nav>

                <div class="p-4 border-t border-white/10">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-white/5 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l-5-5 5-5M11 12h10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Về trang người dùng
                    </a>
                </div>
            </aside>

            {{-- Lớp phủ khi mở sidebar trên mobile --}}
            <div x-show="sidebar" x-cloak @click="sidebar = false"
                 class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

            {{-- Nội dung --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="sticky top-0 z-20 h-[72px] bg-white border-b flex items-center justify-between gap-4 px-6">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button" @click="sidebar = true" class="lg:hidden text-gray-500 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-xl font-bold truncate">{{ $title ?? 'Quản trị' }}</h1>
                            @isset($subtitle)
                                <p class="text-sm text-gray-500 truncate">{{ $subtitle }}</p>
                            @endisset
                        </div>
                    </div>

                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full border border-gray-200 hover:bg-gray-50">
                            <span class="w-8 h-8 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </span>
                            <span class="hidden sm:block text-sm font-semibold max-w-[140px] truncate">{{ auth()->user()->username }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 text-gray-400">
                                <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border py-1 z-40">
                            <div class="px-4 py-3 border-b">
                                <div class="font-semibold truncate">{{ auth()->user()->username }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Hồ sơ cá nhân
                            </a>
                            <div class="border-t"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                @if (session('status'))
                    <div class="mx-6 mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-[#2D5A3D] px-5 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mx-6 mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-[#2D5A3D] px-5 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-6 mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-6 mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-3">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
