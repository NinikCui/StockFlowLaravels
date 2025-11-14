@php
    // ========== SESSION LOGIN ========== //
    $username    = session('user.username', 'User');
    $companyCode = session('role.company.code');
    $branchCode  = session('role.branch.code');
    $roleCode    = session('role.code', 'USER');

    // Tentukan tenant prefix
    $prefix = strtolower($branchCode ?? $companyCode);

    // ========== MENU ITEMS (fixed href) ========== //
    $items = [
        [
            'label' => 'Dashboard',
            'href'  => "/$prefix/dashboard",
        ],

        [
            'label' => 'Cabang Restoran',
            'href'  => "/$prefix/cabang",
            'children' => [
                ['label' => 'Daftar Cabang', 'href' => "/$prefix/cabang"],
                ['label' => 'Gudang',         'href' => "/$prefix/cabang/gudang"],
                ['label' => 'Supplier',       'href' => "/$prefix/cabang/supplier"],
            ]
        ],

        [
            'label' => 'Pegawai',
            'href'  => "/$prefix/pegawai",
            'children' => [
                ['label' => 'Daftar Pegawai', 'href' => "/$prefix/pegawai"],
                ['label' => 'Role & Akses',   'href' => "/$prefix/pegawai/roles"],
            ]
        ],
    ];

    // ===== ACTIVE CHECK FUNCTIONS =====

    function isActiveExact($href) {
        return trim(request()->path(), '/') === trim($href, '/');
    }

    function isActiveStartWith($href) {
        return str_starts_with(trim(request()->path(), '/'), trim($href, '/'));
    }

    function isParentOpen($item) {
        if (!isset($item['children'])) return false;

        foreach ($item['children'] as $child) {
            if (isActiveExact($child['href']) || isActiveStartWith($child['href'])) {
                return true;
            }
        }
        return false;
    }
@endphp


{{-- MOBILE TOGGLE --}}
<button
    onclick="toggleSidebar()"
    class="fixed left-4 top-4 z-40 grid h-10 w-10 place-items-center rounded-xl border bg-white text-gray-800 shadow-sm hover:bg-gray-50 md:hidden"
>
    <div class="relative h-3.5 w-4">
        <span class="absolute block h-0.5 w-4 bg-current top-0"></span>
        <span class="absolute block h-0.5 w-4 bg-current top-1.5"></span>
        <span class="absolute block h-0.5 w-4 bg-current top-3"></span>
    </div>
</button>

{{-- BACKDROP --}}
<div id="sidebar-backdrop"
     onclick="toggleSidebar()"
     class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm hidden md:hidden"></div>

{{-- SIDEBAR --}}
<aside id="sidebar"
    class="fixed left-0 top-0 z-40 flex h-full w-64 flex-col border-r bg-white/95 backdrop-blur 
           transition-transform duration-200 ease-in-out -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="flex items-center gap-2 px-4 py-5 border-b">
        <div class="h-9 w-9 grid place-items-center rounded-lg bg-black text-white font-semibold text-lg">
            R
        </div>
        <div>
            <div class="text-sm font-semibold">RestoApp</div>
            <div class="text-xs text-gray-500 uppercase">
                {{ $companyCode }}
                @if ($branchCode)
                    • {{ $branchCode }}
                @endif
                • {{ $roleCode }}
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isActiveItem = isActiveExact($item['href']) || isActiveStartWith($item['href']);
                $isOpen = $isActiveItem || isParentOpen($item);
            @endphp

            {{-- ========== MENU TANPA CHILDREN ========== --}}
            @if (!$hasChildren)
                <a href="{{ $item['href'] }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all
                    {{ $isActiveItem ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-100 hover:text-black' }}">
                    {{ $item['label'] }}
                </a>

            @else
            {{-- ========== MENU DENGAN CHILDREN ========== --}}
                <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium
                            {{ $isOpen ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-black' }}">
                        <span>{{ $item['label'] }}</span>
                        <svg :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transition-transform">
                            <path fill="currentColor" d="M6 9l6 3-6 3z"/>
                        </svg>
                    </button>

                    {{-- SUBMENU --}}
                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">

                        @foreach ($item['children'] as $child)
                            @php
                                // ===== CHILD ACTIVE CHECK =====
                                if ($child['href'] === "/$prefix/pegawai") {
                                    // Child 1: exact match
                                    $childActive = isActiveExact($child['href']);
                                } else {
                                    // Child lain: prefix match
                                    $childActive = isActiveStartWith($child['href']);
                                }
                            @endphp

                            <a href="{{ $child['href'] }}"
                                class="block rounded-md px-3 py-1.5 text-sm
                                {{ $childActive ? 'bg-gray-800 text-white' : 'text-gray-600 hover:text-black hover:bg-gray-100' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach

                    </div>
                </div>
            @endif

        @endforeach

    </nav>

    {{-- FOOTER --}}
    <div class="border-t bg-gray-50 px-4 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-9 w-9 grid place-items-center rounded-full bg-black text-white">
                {{ strtoupper(substr($username, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="truncate text-sm font-semibold">{{ $username }}</div>
                <div class="truncate text-xs text-gray-500 uppercase">{{ $roleCode }}</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="flex w-full items-center justify-center gap-2 rounded-lg bg-white px-3 py-2 text-sm
                           text-gray-700 ring-1 ring-gray-200 hover:bg-gray-100 hover:text-black">
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- JS --}}
<script>
    function toggleSidebar() {
        const sb = document.getElementById('sidebar');
        const bd = document.getElementById('sidebar-backdrop');

        const hidden = sb.classList.contains('-translate-x-full');
        sb.classList.toggle('-translate-x-full', !hidden);
        bd.classList.toggle('hidden', !hidden);
    }
</script>
