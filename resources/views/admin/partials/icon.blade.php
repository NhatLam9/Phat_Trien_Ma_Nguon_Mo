@php($icon = $icon ?? 'dot')
<svg class="admin-nav__icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
    @switch($icon)
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="2"/>
            <rect x="14" y="3" width="7" height="7" rx="2"/>
            <rect x="3" y="14" width="7" height="7" rx="2"/>
            <rect x="14" y="14" width="7" height="7" rx="2"/>
            @break

        @case('questions')
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H19v16H6.5A2.5 2.5 0 0 0 4 21.5z"/>
            <path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H19"/>
            <path d="M8 7h7M8 11h5"/>
            @break

        @case('types')
            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
            <path d="m3 12 9 5 9-5"/>
            <path d="m3 16 9 5 9-5"/>
            @break

        @case('categories')
            <path d="M20.6 13.6 11 23l-9-9V3h11l7.6 7.6a2 2 0 0 1 0 3Z"/>
            <circle cx="7.5" cy="8.5" r="1.5"/>
            @break

        @case('exam')
            <path d="M9 3h6a2 2 0 0 1 2 2v1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h1V5a2 2 0 0 1 2-2Z"/>
            <path d="M9 5h6M8 12h8M8 16h5"/>
            @break

        @case('users')
            <circle cx="9" cy="8" r="3.2"/>
            <path d="M3 20a6 6 0 0 1 12 0"/>
            <path d="M16 5.5a3 3 0 0 1 0 5.8M18 14.5a5 5 0 0 1 3 4.5"/>
            @break

        @case('ai')
            <path d="m12 3 1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3Z"/>
            <path d="m18.5 15 .9 2.1 2.1.9-2.1.9-.9 2.1-.9-2.1-2.1-.9 2.1-.9.9-2.1Z"/>
            @break

        @default
            <circle cx="12" cy="12" r="3"/>
    @endswitch
</svg>
