@props(['title', 'icon', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1 mb-1">
    <button @click="open = !open" type="button" class="w-full group flex items-center justify-between px-3 py-2.5 min-h-[44px] text-sm font-medium rounded-lg transition-colors active:bg-gray-100 {{ $active ? 'text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        <div class="flex items-center">
            <span class="material-symbols-outlined mr-3 text-[20px] {{ $active ? 'text-[#5586DB]' : 'text-gray-400 group-hover:text-gray-500' }}">
                {{ $icon }}
            </span>
            {{ $title }}
        </div>
        <span class="material-symbols-outlined text-[18px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }">
            expand_more
        </span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="pl-[42px] pr-3 space-y-1" style="display: none;">
        {{ $slot }}
    </div>
</div>
