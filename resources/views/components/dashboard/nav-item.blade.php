@props(['href', 'icon' => null, 'active' => false, 'isChild' => false, 'comingSoon' => false])

<a href="{{ $href }}" class="group flex items-center justify-between {{ $isChild ? 'px-3 py-2 text-[13.5px]' : 'px-3 py-2.5 text-sm' }} min-h-[44px] font-medium rounded-lg transition-colors active:bg-gray-100 {{ $active ? 'bg-[#EEF4FF] text-[#5586DB]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <div class="flex items-center">
        @if($icon)
        <span class="material-symbols-outlined mr-3 {{ $isChild ? 'text-[18px]' : 'text-[20px]' }} {{ $active ? 'text-[#5586DB]' : 'text-gray-400 group-hover:text-gray-500' }}">
            {{ $icon }}
        </span>
        @elseif($isChild)
        <span class="w-[6px] h-[6px] rounded-full mr-3 {{ $active ? 'bg-[#5586DB]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
        @endif
        {{ $slot }}
    </div>
    
    @if($comingSoon)
    <span class="text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-400 font-jakarta ml-2 shrink-0">
        Segera
    </span>
    @endif
</a>
