@props(['paginator'])

<style>
    .pagination-btn { padding: 6px 12px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 35px; border-radius: 0; text-decoration: none !important; }
    .pagination-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .pagination-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .pagination-btn.is-disabled { opacity: 0.4; cursor: not-allowed; background: #f1f5f9; }
</style>

<div class="flex items-center justify-between px-4 py-2">
    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
        {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
    </div>
    <div class="flex items-center gap-1">
        @if ($paginator->onFirstPage())
            <span class="pagination-btn is-disabled"><i class="mdi mdi-chevron-left" aria-hidden="true"></i></span>
        @else
            <a class="pagination-btn" href="{{ $paginator->previousPageUrl() }}"><i class="mdi mdi-chevron-left" aria-hidden="true"></i></a>
        @endif

        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <a class="pagination-btn {{ $paginator->currentPage() === $i ? 'active' : '' }}" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        @endfor

        @if ($paginator->hasMorePages())
            <a class="pagination-btn" href="{{ $paginator->nextPageUrl() }}"><i class="mdi mdi-chevron-right" aria-hidden="true"></i></a>
        @else
            <span class="pagination-btn is-disabled"><i class="mdi mdi-chevron-right" aria-hidden="true"></i></span>
        @endif
    </div>
</div>
