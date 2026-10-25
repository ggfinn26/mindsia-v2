{{--
    Coverflow gallery carousel - reusable across landing pages.
    Params:
      $items     Collection of ActivityPhoto (->photo_url, ->title, ->caption)
      $sectionId string  anchor id (gallery / galeri)
      $bg        string  Tailwind bg class for the section
      $eyebrow   string  small uppercase label
      $title     string  heading
      $subtitle  string  sub text
--}}
<section class="py-24 px-6 {{ $bg ?? 'bg-white' }} overflow-hidden" id="{{ $sectionId }}" aria-label="{{ $title }}">
    <div class="max-w-[1200px] mx-auto">

        <div class="text-center mb-14">
            <p class="font-sans font-bold text-sm text-ky-primary tracking-widest uppercase mb-3">{{ $eyebrow }}</p>
            <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-3">{{ $title }}</h2>
            <p class="text-[16px] text-ky-text/70 max-w-xl mx-auto">{{ $subtitle }}</p>
        </div>

        @if($items->isNotEmpty())
        @php
            $catIds = $items->map(fn ($i) => $i->category_id)->values();
            $cats = $items->pluck('category')->filter()->unique('id')->sortBy('sort_order')->values();
        @endphp
        <div x-data="galleryCoverflow({ cats: {{ $catIds->toJson() }} })">

            {{-- category filter chips --}}
            @if($cats->count() > 1)
            <div class="flex flex-wrap justify-center gap-2.5 mb-12">
                <button type="button" @click="setCat(null)"
                        class="px-5 py-2 rounded-full text-sm font-semibold border transition-colors"
                        style="border-color: rgba(6,18,43,0.12);"
                        :style="activeCat === null ? 'background-color:#06122B;color:#fff;border-color:#06122B' : 'background-color:#fff;color:#06122B'">Semua</button>
                @foreach($cats as $cat)
                <button type="button" @click="setCat('{{ $cat->id }}')"
                        class="px-5 py-2 rounded-full text-sm font-semibold border transition-colors"
                        style="border-color: rgba(6,18,43,0.12);"
                        :style="String(activeCat) === '{{ $cat->id }}' ? 'background-color:#06122B;color:#fff;border-color:#06122B' : 'background-color:#fff;color:#06122B'">{{ $cat->name }}</button>
                @endforeach
            </div>
            @endif

            {{-- stage --}}
            <div class="relative h-[480px] md:h-[640px]" style="perspective: 1600px;">
                @foreach($items as $index => $item)
                <div class="absolute left-1/2 top-0 w-[330px] h-[460px] md:w-[480px] md:h-[620px] transition-all duration-500 ease-out cursor-pointer"
                     style="transform-style: preserve-3d;"
                     :style="slideStyle({{ $index }})"
                     @click="go({{ $index }})"
                     aria-label="{{ $item->title }}">
                    <div class="w-full h-full rounded-3xl overflow-hidden shadow-[0_25px_50px_-12px_rgba(6,18,43,0.35)] bg-ky-surface">
                        <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>
                @endforeach
            </div>

            {{-- caption of active slide --}}
            <div class="relative mt-8 h-20 md:h-24">
                @foreach($items as $index => $item)
                <div class="absolute inset-0 flex flex-col items-center justify-start text-center px-4"
                     x-show="activeSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display: none;">
                    <h3 class="font-sans font-black text-2xl md:text-3xl text-[#06122B] tracking-tight mb-2">{{ $item->title }}</h3>
                    @if($item->caption)
                    <p class="text-[15px] md:text-[16px] text-ky-text/70 leading-relaxed max-w-lg">{{ $item->caption }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- arrows --}}
            @if($items->count() > 1)
            <div class="flex justify-center items-center gap-4 mt-6">
                <button type="button" @click="prev()" aria-label="Sebelumnya"
                        class="w-12 h-12 rounded-full border border-slate-300 flex items-center justify-center text-[#06122B] hover:bg-[#06122B] hover:text-white hover:border-[#06122B] transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button" @click="next()" aria-label="Berikutnya"
                        class="w-12 h-12 rounded-full border border-slate-300 flex items-center justify-center text-[#06122B] hover:bg-[#06122B] hover:text-white hover:border-[#06122B] transition-colors">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
            @endif
        </div>
        @else
        <div class="p-12 border border-dashed border-slate-200 text-slate-400 text-center rounded-2xl max-w-md mx-auto">
            Belum ada foto galeri.
        </div>
        @endif
    </div>
</section>
