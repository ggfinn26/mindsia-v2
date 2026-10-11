@props(['title', 'icon', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1 mb-1">
    <button @click="open = !open" type="button"
            class="w-full flex items-center justify-between px-3 py-2.5 min-h-[44px] text-sm font-medium rounded-lg transition-colors"
            style="{{ $active
                ? 'color: var(--dash-text-heading);'
                : 'color: var(--dash-nav-inactive);' }}"
            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)'"
            onmouseout="this.style.backgroundColor='transparent'; this.style.color='{{ $active ? 'var(--dash-text-heading)' : 'var(--dash-nav-inactive)' }}'">
        <div class="flex items-center min-w-0">
            <span class="material-symbols-outlined mr-3 text-[20px] shrink-0"
                  style="{{ $active ? 'color: var(--dash-primary);' : 'color: var(--dash-nav-icon-inactive);' }}">
                {{ $icon }}
            </span>
            <span class="truncate">{{ $title }}</span>
        </div>
        <span class="material-symbols-outlined text-[18px] shrink-0 transition-transform duration-200"
              style="color: var(--dash-nav-icon-inactive);"
              :class="{ 'rotate-180': open }">
            expand_more
        </span>
    </button>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="pl-[42px] pr-3 space-y-1"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
