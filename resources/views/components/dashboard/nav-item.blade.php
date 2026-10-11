@props(['href', 'icon' => null, 'active' => false, 'isChild' => false, 'comingSoon' => false])

<a href="{{ $href }}"
   class="flex items-center justify-between {{ $isChild ? 'px-3 py-2 text-[13.5px]' : 'px-3 py-2.5 text-sm' }} min-h-[44px] font-medium rounded-lg transition-colors"
   style="{{ $active
       ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary);'
       : 'color: var(--dash-nav-inactive);' }}"
   @if(!$active)
   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)'"
   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--dash-nav-inactive)'"
   @endif>
    <div class="flex items-center min-w-0">
        @if($icon)
        <span class="material-symbols-outlined mr-3 shrink-0 {{ $isChild ? 'text-[18px]' : 'text-[20px]' }}"
              style="{{ $active ? 'color: var(--dash-primary);' : 'color: var(--dash-nav-icon-inactive);' }}">
            {{ $icon }}
        </span>
        @elseif($isChild)
        <span class="w-[6px] h-[6px] rounded-full mr-3 shrink-0"
              style="{{ $active ? 'background-color: var(--dash-primary);' : 'background-color: var(--dash-nav-icon-inactive);' }}"></span>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </div>

    @if($comingSoon)
    <span class="text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded font-jakarta ml-2 shrink-0"
          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-faint);">
        Segera
    </span>
    @endif
</a>
