<div class="flex items-center gap-1" x-data="{ rating: {{ $currentRating ?? 0 }}, hover: 0 }">
    @for($i = 1; $i <= 5; $i++)
    <button type="button"
        @click="rating = {{ $i }}"
        @mouseenter="hover = {{ $i }}"
        @mouseleave="hover = 0"
        class="focus:outline-none cursor-pointer">
        <span class="material-symbols-outlined transition-colors text-[24px]"
            :class="(hover || rating) >= {{ $i }} ? 'text-amber-400' : ''"
            :style="(hover || rating) < {{ $i }} ? 'color: var(--dash-text-faint);' : ''">star</span>
    </button>
    @endfor
    <span class="text-xs font-semibold ml-2" style="color: var(--dash-text-muted);" x-text="rating ? rating + ' / 5 Bintang' : 'Pilih rating'"></span>
    <input type="hidden" name="rating" :value="rating">
</div>
