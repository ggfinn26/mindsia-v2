@props(['title', 'icon', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1 mb-1">
    <button @click="open = !open" type="button"
            class="w-full group flex items-center justify-between px-3 py-2.5 min-h-[44px] text-sm font-medium rounded-lg transition-colors"
            @if($active)
                style="color: var(--dash-text-heading);"
            @else
                style="color: var(--dash-nav-inactive);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)';"
                onmouseout="this.style.backgroundColor=''; this.style.color='var(--dash-nav-inactive)';"
            @endif
    >
        <div class="flex items-center">
            <span class="material-symbols-outlined mr-3 text-[20px]"
                  style="color: {{ $active ? 'var(--dash-primary)' : 'var(--dash-nav-icon-inactive)' }}">
                {{ $icon }}
            </span>
            {{ $title }}
        </div>
        <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
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
