@php
    use Illuminate\Support\Str;

    $username = session('user.username', 'User');
    $roleCode = session('role.code', 'USER');

    $role  = session('role');
    $scope = $role['scope'];
    $code  = strtolower(
        $scope === 'COMPANY'
            ? $role['company']['code']
            : $role['branch']['code']
    );

    $items = menuItems();

    // ============================
    // tenantHref()
    // ============================
    if (!function_exists('tenantHref')) {
        function tenantHref($href)
        {
            $role  = session('role');
            $scope = $role['scope'];
            $code  = strtolower(
                $scope === 'COMPANY'
                    ? $role['company']['code']
                    : $role['branch']['code']
            );

            $prefix = $scope === 'COMPANY'
                ? "company/$code"
                : "branch/$code";

            // ========== ANTIDUP PREFIX ==========
            if (\Illuminate\Support\Str::startsWith($href, $prefix)) {
                return '/' . trim($href, '/');
            }

            if (\Illuminate\Support\Str::startsWith($href, '/'.$prefix)) {
                return '/' . trim($href, '/');
            }

            // ========== DASHBOARD ==========
            if ($href === 'dashboard') {
                $res = $scope === 'COMPANY'
                    ? "/company/$code/dashboard"
                    : "/branch/$code/dashboard";
                return $res;
            }

            // ========== NORMAL CASE ==========
            $final = "/$prefix/" . trim($href, '/');
            return $final;
        }
    }

    // ============================
    // isActive
    // ============================
    if (!function_exists('isActive')) {
        function isActive($href)
        {
            $final = tenantHref($href);
            return request()->is(ltrim($final, '/'))
                || request()->is(ltrim($final, '/').'/*');
        }
    }

    // ============================
    // anyChildActive
    // ============================
    if (!function_exists('anyChildActive')) {
        function anyChildActive($children)
        {
            foreach ($children as $c) {
                if (isActive($c['href'])) return true;
            }
            return false;
        }
    }
@endphp

{{-- Mobile Menu Button --}}
<button onclick="toggleSidebar()" 
        class="fixed left-4 top-4 z-50 md:hidden h-12 w-12 rounded-xl bg-white border-2 border-gray-200 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
    <svg class="h-6 w-6 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- Backdrop --}}
<div id="sidebar-backdrop" onclick="toggleSidebar()" 
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300"></div>

{{-- Sidebar --}}
<aside id="sidebar"
    class="fixed left-0 top-0 z-40 h-full w-72 bg-gradient-to-b from-white to-gray-50 border-r border-gray-200 flex flex-col shadow-2xl
           transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="relative px-6 py-6 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-4">
            {{-- Logo/Icon --}}
            <div class="relative">
                <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl grid place-items-center shadow-lg shadow-emerald-500/30">
                    <span class="font-bold text-xl text-white">R</span>
                </div>
                <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-400 rounded-full border-2 border-white"></div>
            </div>

            {{-- Company Info --}}
            <div class="flex-1 min-w-0">
                <div class="font-bold text-lg text-gray-900 truncate">
                    {{ strtoupper($role['company']['code']) }}
                </div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full mt-1">
                    <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    {{ strtoupper($roleCode) }}
                </div>
            </div>
        </div>
    </div>

    {{-- NAV MENU --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2 custom-scrollbar">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isOpen = $hasChildren
                    ? anyChildActive($item['children'])
                    : (!($item['parent_only'] ?? false) && isActive($item['href'] ?? ''));
            @endphp

            {{-- MENU TANPA CHILDREN --}}
            @if (!$hasChildren)
                <a href="{{ tenantHref($item['href']) }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200
                    {{ isActive($item['href']) 
                        ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-500/40' 
                        : 'text-gray-700 hover:bg-white hover:shadow-md hover:scale-[1.02]' }}">
                    
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors
                        {{ isActive($item['href']) ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-emerald-50' }}">
                        @include('components.sidebar-icon', ['name' => $item['icon']])
                    </div>
                    
                    <span class="flex-1">{{ $item['label'] }}</span>
                    
                    @if(isActive($item['href']))
                        <div class="h-2 w-2 rounded-full bg-white animate-pulse"></div>
                    @endif
                </a>

            @else
                {{-- MENU DENGAN CHILDREN --}}
                <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }" class="space-y-1">

                    {{-- Parent Button --}}
                    <button @click="open = !open"
                            class="group flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200
                                   {{ $isOpen 
                                       ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-500/40' 
                                       : 'text-gray-700 hover:bg-white hover:shadow-md hover:scale-[1.02]' }}">
                        
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors
                                {{ $isOpen ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-emerald-50' }}">
                                @include('components.sidebar-icon', ['name' => $item['icon']])
                            </div>
                            <span>{{ $item['label'] }}</span>
                        </div>

                        <svg :class="open ? 'rotate-180' : ''" 
                             class="h-5 w-5 transition-transform duration-300 flex-shrink-0">
                            <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    {{-- Children --}}
                    <div x-show="open" 
                         x-collapse 
                         class="ml-11 pl-4 space-y-1 border-l-2 transition-colors duration-200
                                {{ $isOpen ? 'border-emerald-400' : 'border-gray-200' }}">
                        @foreach ($item['children'] as $child)
                            <a href="{{ tenantHref($child['href']) }}"
                               class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all duration-200
                               {{ isActive($child['href'])
                                   ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm' 
                                   : 'text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:translate-x-1' }}">
                                
                                <span class="h-1.5 w-1.5 rounded-full transition-all duration-200
                                    {{ isActive($child['href']) 
                                        ? 'bg-emerald-500 ring-2 ring-emerald-200' 
                                        : 'bg-gray-300 group-hover:bg-emerald-400' }}"></span>
                                
                                <span class="flex-1">{{ $child['label'] }}</span>
                                
                                @if(isActive($child['href']))
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>

                </div>
            @endif

        @endforeach
    </nav>

    {{-- FOOTER --}}
    <div class="border-t border-gray-200 bg-white px-4 py-4">
        {{-- User Info --}}
        <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
            <div class="relative flex-shrink-0">
                <div class="h-11 w-11 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 grid place-items-center font-bold text-white text-lg shadow-lg">
                    {{ strtoupper(substr($username, 0, 1)) }}
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 bg-green-400 rounded-full border-2 border-white"></div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-gray-900 truncate">{{ $username }}</div>
                <div class="text-xs text-gray-500 font-medium">{{ $roleCode }}</div>
            </div>
        </div>

        {{-- Logout Button --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="group w-full text-sm font-semibold px-4 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 
                           hover:bg-red-50 hover:border-red-200 hover:text-red-600 
                           transition-all duration-200 hover:shadow-md
                           flex items-center justify-center gap-2">
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>

<script>
function toggleSidebar() {
    const side = document.getElementById('sidebar');
    const back = document.getElementById('sidebar-backdrop');
    const hidden = side.classList.contains('-translate-x-full');

    side.classList.toggle('-translate-x-full', !hidden);
    back.classList.toggle('hidden', !hidden);

    document.body.style.overflow = hidden ? 'hidden' : '';
}

// Close sidebar when clicking outside on mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
            toggleSidebar();
        }
    });
});
</script>

<style>
/* Custom Scrollbar untuk Sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Smooth transitions untuk Alpine.js collapse */
[x-cloak] {
    display: none !important;
}

/* Hover effect untuk menu items */
@media (hover: hover) {
    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(0.25rem);
    }
}

/* Animation untuk active indicator */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>