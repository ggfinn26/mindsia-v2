@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Mobile Controls --}}
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-lg opacity-40 cursor-not-allowed"
                      style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-faint);">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    Sebelumnya
                </a>
            @endif

            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">
                Hal <strong style="color: var(--dash-text-heading);">{{ $paginator->currentPage() }}</strong> dari <strong style="color: var(--dash-text-heading);">{{ $paginator->lastPage() }}</strong>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Berikutnya
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-lg opacity-40 cursor-not-allowed"
                      style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-faint);">
                    Berikutnya
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </span>
            @endif
        </div>

        {{-- Desktop Counter --}}
        <div class="hidden sm:block">
            <p class="text-xs font-medium" style="color: var(--dash-text-muted);">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-bold" style="color: var(--dash-text-heading);">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-bold" style="color: var(--dash-text-heading);">{{ $paginator->lastItem() }}</span>
                @else
                    <span class="font-bold" style="color: var(--dash-text-heading);">{{ $paginator->count() }}</span>
                @endif
                dari
                <span class="font-bold" style="color: var(--dash-text-heading);">{{ $paginator->total() }}</span>
                data
            </p>
        </div>

        {{-- Desktop Page Number Buttons --}}
        <div class="hidden sm:flex items-center gap-1.5">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Halaman Sebelumnya"
                      class="inline-flex items-center justify-center w-8 h-8 rounded-lg opacity-40 cursor-not-allowed"
                      style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-faint);">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman Sebelumnya"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors cursor-pointer"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            @endif

            {{-- Page Elements --}}
            @foreach ($elements as $element)
                {{-- Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold"
                          style="color: var(--dash-text-faint);">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold shadow-sm"
                                  style="background-color: var(--dash-primary); color: var(--dash-primary-text); border: 1px solid var(--dash-primary);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'"
                               aria-label="Ke halaman {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman Berikutnya"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors cursor-pointer"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            @else
                <span aria-disabled="true" aria-label="Halaman Berikutnya"
                      class="inline-flex items-center justify-center w-8 h-8 rounded-lg opacity-40 cursor-not-allowed"
                      style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-faint);">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </span>
            @endif
        </div>
    </nav>
@endif
