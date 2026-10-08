@props(['href', 'icon' => null, 'active' => false, 'isChild' => false, 'comingSoon' => false])

<a href="{{ $href }}"
   class="group flex items-center justify-between {{ $isChild ? 'px-3 py-2 text-[13.5px]' : 'px-3 py-2.5 text-sm' }} min-h-[44px] font-medium rounded-lg transition-colors"
   @if($active)
       style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);"
   @else
       style="color: var(--dash-nav-inactive);"
       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)';"
       onmouseout="this.style.backgroundColor=''; this.style.color='var(--dash-nav-inactive)';"
   @endif
>
    <div class="flex items-center">
        @if($icon)
        <span class="material-symbols-outlined mr-3 {{ $isChild ? 'text-[18px]' : 'text-[20px]' }}"
              style="color: {{ $active ? 'var(--dash-primary)' : 'var(--dash-nav-icon-inactive)' }}">
            {{ $icon }}
        </span>
        @elseif($isChild)
        <span class="w-[6px] h-[6px] rounded-full mr-3"
              style="background-color: {{ $active ? 'var(--dash-primary)' : 'var(--dash-nav-icon-inactive)' }}"></span>
        @endif
        {{ $slot }}
    </div>

    @if($comingSoon)
    <span class="text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded font-jakarta ml-2 shrink-0"
          style="background-color: var(--dash-card-border); color: var(--dash-text-faint);">
        Segera
    </span>
    @endif
</a>
