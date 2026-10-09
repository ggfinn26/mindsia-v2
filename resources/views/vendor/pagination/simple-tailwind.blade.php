@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg cursor-not-allowed"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg cursor-not-allowed"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
