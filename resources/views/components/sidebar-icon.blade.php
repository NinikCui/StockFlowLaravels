@switch($name)
    @case('home')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7m-9 13v-4h4v4m-8 0h12"/>
        </svg>
        @break

    @case('store')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 7h18M3 7l2 10a2 2 0 002 2h10a2 2 0 002-2l2-10M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2"/>
        </svg>
        @break

    @case('users')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M12 4a3 3 0 110 6 3 3 0 010-6z"/>
        </svg>
        @break

    @case('box')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0L12 11m8-4v10l-8 4m0-10L4 7m8 4v10"/>
        </svg>
        @break

    @case('cart')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7m-8 4V7m13 2H3l1 12h16l1-12z"/>
        </svg>
        @break

    @case('layers')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 9l9 6 9-6M3 9l9-6 9 6M3 15l9 6 9-6"/>
        </svg>
        @break

    @case('settings')
        <svg class="w-5 h-5" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.75 3a1.75 1.75 0 013.5 0m-6.49 2.75L5.5 5.5m13 0l1.24 1.25M3 12a1.75 1.75 
                 0 013.5 0m12 0a1.75 1.75 0 013.5 0M6.75 20a1.75 1.75 0 01-3.5 0m16.49 0a1.75 1.75 
                 0 01-3.5 0"/>
        </svg>
        @break
@endswitch
