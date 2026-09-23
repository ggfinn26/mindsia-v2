<div class="flex items-center gap-1" x-data="{ rating: {{ $currentRating ?? 0 }}, hover: 0 }">
    @for($i = 1; $i <= 5; $i++)
    <button type="button"
        @click="rating = {{ $i }}"
        @mouseenter="hover = {{ $i }}"
        @mouseleave="hover = 0"
        class="focus:outline-none">
        <span class="material-symbols-outlined transition-colors"
            :class="(hover || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-200'"
            style="font-size:24px">star</span>
    </button>
    @endfor
    <span class="text-xs text-gray-500 ml-1" x-text="rating ? rating + '/5' : 'Pilih rating'"></span>
    <input type="hidden" name="rating" :value="rating">
</div>
